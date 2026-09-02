<?php

namespace App\Database;

use App\Database\Db;
use App\Database\Builder;

class Model
{
    protected static $table;
    protected $attributes = [];
    protected $original = [];
    protected $relations = [];
    protected static array $fillable = [];
    protected static array $guarded = ['*'];

    // ================ روش جدید (non-static) ================
    public static function query()
    {
        return new Builder(static::$table, static::class);
    }

    public static function __callStatic($method, $arguments)
    {
        // اگر متد استاتیک در کلاس جاری وجود ندارد، به Builder بسپار
        $builder = static::query();
        if (method_exists($builder, $method)) {
            $result = $builder->$method(...$arguments);
            // اگر متد get, first, paginate, count, insert, update, delete بود، نتیجه را برگردان
            if (in_array($method, ['get', 'first', 'paginate', 'count', 'insert', 'update', 'delete'])) {
                return $result;
            }
            // در غیر این صورت خود builder را برگردان برای زنجیره‌ای کردن
            return $result;
        }
        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function find($id)
    {
        return static::query()->where('id', '=', $id)->first();
    }

    public static function create($data)
    {
        $instance = new static();
        $data = $instance->filterFillable($data);
        $id = static::query()->insert($data);
        return static::find($id);
    }

    // متدهای نمونه (برای اشیاء مدل)
    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
        $this->original = $attributes;
    }

    public function save()
    {
        if (isset($this->attributes['id'])) {
            $data = $this->getDirty();
            if (empty($data)) return true;
            return $this->update($data);
        } else {
            // insert
            $id = static::query()->insert($this->attributes);
            if ($id) {
                $this->attributes['id'] = $id;
                $this->original = $this->attributes;
                return true;
            }
            return false;
        }
    }

    public function update($data)
    {
        if (!isset($this->attributes['id'])) return false;
        $data = $this->filterFillable($data);
        $builder = static::query()->where('id', '=', $this->attributes['id']);
        $affected = $builder->update($data);
        if ($affected) {
            $this->attributes = array_merge($this->attributes, $data);
            $this->original = $this->attributes;
        }
        return $affected > 0;
    }

    public function delete()
    {
        if (!isset($this->attributes['id'])) return false;
        $builder = static::query()->where('id', '=', $this->attributes['id']);
        $affected = $builder->delete();
        if ($affected) {
            $this->attributes = [];
            $this->original = [];
        }
        return $affected > 0;
    }

    public function getDirty()
    {
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }

    public function hasMany($related, $foreignKey = null, $localKey = 'id')
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        return new Relations\HasMany($this, $related, $foreignKey, $localKey);
    }

    public function belongsTo($related, $foreignKey = null, $ownerKey = 'id')
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        return new Relations\BelongsTo($this, $related, $foreignKey, $ownerKey);
    }

    public function hasOne($related, $foreignKey = null, $localKey = 'id')
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        return new Relations\HasOne($this, $related, $foreignKey, $localKey);
    }

    public function belongsToMany($related, $pivotTable = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null)
    {
        $instance = new $related;
        $pivotTable = $pivotTable ?: $this->getTable() . '_' . $instance->getTable();
        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();
        $parentKey = $parentKey ?: 'id';
        $relatedKey = $relatedKey ?: 'id';
        return new Relations\BelongsToMany($this, $related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey);
    }

    public function morphMany($related, $name, $type = null, $id = null, $localKey = 'id')
    {
        $type = $type ?: $name . '_type';
        $id = $id ?: $name . '_id';
        return new Relations\MorphMany($this, $related, $type, $id, $localKey);
    }

    public function morphTo($name = null, $type = null, $id = null)
    {
        $name = $name ?: $this->getMorphClass();
        $type = $type ?: $name . '_type';
        $id = $id ?: $name . '_id';
        return new Relations\MorphTo($this, $type, $id);
    }

    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null)
    {
        return new Relations\MorphToMany($this, $related, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey);
    }

    protected function getMorphClass()
    {
        return get_class($this);
    }

    protected function getForeignKey()
    {
        $className = basename(str_replace('\\', '/', get_class($this)));
        return strtolower($className) . '_id';
    }

    public function setRelation($name, $value)
    {
        $this->relations[$name] = $value;
    }

    public function __get($key)
    {
        // 1. اگر در کش روابط وجود دارد
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        // 2. اگر متد رابطه تعریف شده است
        if (method_exists($this, $key)) {
            $relation = $this->$key();
            if ($relation instanceof Relation) {
                return $this->relations[$key] = $relation->getResults();
            }
        }
        // 3. در غیر این صورت، attribute
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function fill(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $this->filterFillable($attributes));
        return $this;
    }

    protected function filterFillable(array $attributes): array
    {
        if (static::$guarded === ['*'] && empty(static::$fillable)) return [];
        return array_filter($attributes, function ($value, $key) {
            if (!empty(static::$fillable)) return in_array($key, static::$fillable, true);
            return !in_array($key, static::$guarded, true);
        }, ARRAY_FILTER_USE_BOTH);
    }

    // ================ متدهای کمکی ================
    public static function getTable()
    {
        return static::$table;
    }

    public static function setTable($table)
    {
        static::$table = $table;
    }

    public static function getTableColumns()
    {
        $table = static::getTable();
        $db = Db::getInstance();
        $table = static::getTable();
        if (!is_string($table) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)) throw new \InvalidArgumentException('Invalid table identifier.');
        $result = $db->query("DESCRIBE `{$table}`");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = [
                'Field' => $row['Field'],
                'Type' => $row['Type'],
                'Null' => $row['Null'],
                'Key' => $row['Key'],
                'Default' => $row['Default'],
                'Extra' => $row['Extra'],
            ];
        }
        return $columns;
    }
}

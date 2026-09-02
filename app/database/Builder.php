<?php

namespace App\Database;

use App\Database\Db;
use App\Database\SqlGuard;

class Builder
{
    protected $table;
    protected $modelClass;
    protected $withConstraints = [];
    protected $select = ['*'];
    protected $conditions = [];
    protected $bindings = [];
    protected $orderBy = [];
    protected $limit = null;
    protected $offset = null;
    protected $isWhereUsed = false;
    public $with = [];


    public function __construct($table, $modelClass = null)
    {
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    // public function with($relations)
    // {
    //     $this->with = is_array($relations) ? $relations : func_get_args();
    //     return $this;
    // }

    public function with($relations)
    {
        $this->with = [];
        $this->withConstraints = [];

        if (is_string($relations)) {
            $this->with[] = $relations;
        } elseif (is_array($relations)) {
            foreach ($relations as $key => $value) {
                if (is_string($key) && is_callable($value)) {
                    // حالت ['books' => function($q) { ... }]
                    $this->with[] = $key;
                    $this->withConstraints[$key] = $value;
                } else {
                    // حالت ['books', 'author'] یا ['books']
                    $this->with[] = $value;
                }
            }
        }
        return $this;
    }

    public function select($columns)
    {
        if (is_array($columns)) {
            $this->select = $columns;
        } else {
            $this->select = func_get_args();
        }
        return $this;
    }


    protected function identifier($name)
    {
        return SqlGuard::identifier((string) $name);
    }

    protected function operator($operator)
    {
        return SqlGuard::operator((string) $operator);
    }

    protected function compileConditions()
    {
        $sql = '';
        foreach ($this->conditions as $index => $item) {
            $sql .= ($index === 0 ? '' : ' ' . $item[1] . ' ') . $item[0];
        }
        return $sql;
    }

    public function where($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->conditions[] = [$this->identifier($column) . ' ' . $this->operator($operator) . ' ?', 'AND'];
        $this->bindings[] = $value;
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->conditions[] = [$this->identifier($column) . ' ' . $this->operator($operator) . ' ?', 'OR'];
        $this->bindings[] = $value;
        $this->isWhereUsed = true;
        return $this;
    }

    public function whereIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->conditions[] = [$this->identifier($column) . " IN ($placeholders)", 'AND'];
        foreach ($values as $value) {
            $this->bindings[] = $value;
        }
        return $this;
    }

    // app/database/Builder.php

    /**
     * شرط WHERE با IS NULL
     * 
     * @param string $column نام ستون
     * @param string $boolean نوع ترکیب (AND یا OR)
     * @return $this
     */
    public function whereNull($column, $boolean = 'AND')
    {
        $this->conditions[] = [$this->identifier($column) . ' IS NULL', strtoupper($boolean) === 'OR' ? 'OR' : 'AND'];
        return $this;
    }

    /**
     * شرط WHERE با IS NOT NULL
     * 
     * @param string $column نام ستون
     * @param string $boolean نوع ترکیب (AND یا OR)
     * @return $this
     */
    public function whereNotNull($column, $boolean = 'AND')
    {
        $this->conditions[] = [$this->identifier($column) . ' IS NOT NULL', strtoupper($boolean) === 'OR' ? 'OR' : 'AND'];
        return $this;
    }

    /**
     * شرط OR WHERE با IS NULL
     * 
     * @param string $column نام ستون
     * @return $this
     */
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'OR');
    }

    /**
     * شرط OR WHERE با IS NOT NULL
     * 
     * @param string $column نام ستون
     * @return $this
     */
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'OR');
    }

    public function orderBy($column, $direction = 'asc')
    {
        $direction = SqlGuard::direction((string) $direction);
        $this->orderBy[] = $this->identifier($column) . ' ' . $direction;
        return $this;
    }

    public function limit($value)
    {
        $this->limit = (int)$value;
        return $this;
    }

    public function offset($value)
    {
        $this->offset = (int)$value;
        return $this;
    }

    public function get()
    {
        $columns = $this->select === ['*'] ? '*' : implode(', ', array_map(function ($column) { return $this->identifier($column); }, $this->select));
        $sql = 'SELECT ' . $columns . ' FROM ' . $this->identifier($this->table);

        if (!empty($this->conditions)) {
            $sql .= " WHERE ";
            $parts = [];
            foreach ($this->conditions as $cond) {
                $parts[] = $cond[0];
            }
            $glue = $this->isWhereUsed ? ' ' : ' AND ';
            $sql .= $this->compileConditions();
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
        // SQL bindings may contain sensitive application data; do not log them by default.

        $stmt = Db::getInstance()->prepare($sql);
        if (!empty($this->bindings)) {
            $types = str_repeat('s', count($this->bindings));
            $stmt->bind_param($types, ...$this->bindings);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // مرحله 1: تبدیل ردیف‌ها به مدل‌های واقعی
        $modelClass = $this->modelClass;
        $models = array_map(function ($row) use ($modelClass) {
            return new $modelClass($row);
        }, $rows);

        // مرحله 2: eager loading (اگر درخواست شده باشد)
        // if (!empty($this->with)) {
        //     foreach ($this->with as $relation) {
        //         $models = $this->eagerLoadRelation($models, $relation);
        //     }
        // }
        if (!empty($this->with)) {
            foreach ($this->with as $relation) {
                $constraint = $this->withConstraints[$relation] ?? null;
                $models = $this->eagerLoadRelation($models, $relation, $constraint);
            }
        }

        return new \App\Database\Collection($models);
    }

    public function first()
    {
        $clone = clone $this;
        $clone->limit(1);
        $results = $clone->get();
        return $results[0] ?? null;
    }

    public function count()
    {
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->identifier($this->table);
        if (!empty($this->conditions)) {
            $sql .= " WHERE ";
            $parts = [];
            foreach ($this->conditions as $cond) {
                $parts[] = $cond[0];
            }
            $glue = $this->isWhereUsed ? ' ' : ' AND ';
            $sql .= $this->compileConditions();
        }
        $stmt = Db::getInstance()->prepare($sql);
        if (!empty($this->bindings)) {
            $types = str_repeat('s', count($this->bindings));
            $stmt->bind_param($types, ...$this->bindings);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['total'];
    }

    // public function paginate($perPage = 15, $pageName = 'page', $page = null)
    // {
    //     if ($page === null) {
    //         $page = isset($_GET[$pageName]) ? (int)$_GET[$pageName] : 1;
    //     }
    //     if ($page < 1) $page = 1;

    //     $offset = ($page - 1) * $perPage;
    //     $clone = clone $this;
    //     $clone->limit($perPage)->offset($offset);
    //     $data = $clone->get();

    //     $total = $this->count();
    //     $lastPage = (int)ceil($total / $perPage);
    //     if ($lastPage < 1) $lastPage = 1;

    //     return [
    //         'data'        => $data,
    //         'current_page' => $page,
    //         'per_page'    => $perPage,
    //         'total'       => $total,
    //         'last_page'   => $lastPage,
    //         'from'        => ($page - 1) * $perPage + 1,
    //         'to'          => min($page * $perPage, $total),
    //         'has_pages'   => $lastPage > 1,
    //         'has_prev'    => $page > 1,
    //         'has_next'    => $page < $lastPage,
    //         'prev_page'   => $page > 1 ? $page - 1 : null,
    //         'next_page'   => $page < $lastPage ? $page + 1 : null,
    //     ];
    // }

    public function paginate($perPage = 15, $pageName = 'page', $page = null)
    {
        if ($page === null) {
            $page = isset($_GET[$pageName]) ? (int)$_GET[$pageName] : 1;
        }
        if ($page < 1) $page = 1;

        $offset = ($page - 1) * $perPage;
        $clone = clone $this;
        $clone->limit($perPage)->offset($offset);
        $data = $clone->get();

        $total = $this->count();
        $lastPage = (int)ceil($total / $perPage);
        if ($lastPage < 1) $lastPage = 1;

        $paginationArray = [
            'data'         => $data,
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => $lastPage,
            'page_name'    => $pageName,
            'from'         => ($page - 1) * $perPage + 1,
            'to'           => min($page * $perPage, $total),
            'has_pages'    => $lastPage > 1,
            'has_prev'     => $page > 1,
            'has_next'     => $page < $lastPage,
            'prev_page'    => $page > 1 ? $page - 1 : null,
            'next_page'    => $page < $lastPage ? $page + 1 : null,
        ];

        return new \App\Database\Paginator($paginationArray);
    }

    public function insert($data)
    {
        $fields = implode(',', array_map(function ($field) { return $this->identifier($field); }, array_keys($data)));
        $placeholders = rtrim(str_repeat('?,', count($data)), ',');
        $sql = 'INSERT INTO ' . $this->identifier($this->table) . " ($fields) VALUES ($placeholders)";
        $stmt = Db::getInstance()->prepare($sql);
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();
        return Db::getInstance()->insert_id;
    }

    public function update($data)
    {
        $set = [];
        $values = [];
        foreach ($data as $column => $value) {
            $set[] = $this->identifier($column) . ' = ?';
            $values[] = $value;
        }
        $sql = 'UPDATE ' . $this->identifier($this->table) . ' SET ' . implode(', ', $set);
        if (!empty($this->conditions)) {
            $sql .= " WHERE ";
            $parts = [];
            foreach ($this->conditions as $cond) {
                $parts[] = $cond[0];
            }
            $glue = $this->isWhereUsed ? ' ' : ' AND ';
            $sql .= $this->compileConditions();
        }
        $bindings = array_merge($values, $this->bindings);
        $stmt = Db::getInstance()->prepare($sql);
        $types = str_repeat('s', count($bindings));
        $stmt->bind_param($types, ...$bindings);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->identifier($this->table);
        if (!empty($this->conditions)) {
            $sql .= " WHERE ";
            $parts = [];
            foreach ($this->conditions as $cond) {
                $parts[] = $cond[0];
            }
            $glue = $this->isWhereUsed ? ' ' : ' AND ';
            $sql .= $this->compileConditions();
        }
        $stmt = Db::getInstance()->prepare($sql);
        if (!empty($this->bindings)) {
            $types = str_repeat('s', count($this->bindings));
            $stmt->bind_param($types, ...$this->bindings);
        }
        $stmt->execute();
        return $stmt->affected_rows;
    }

    // protected function eagerLoadRelation($models, $relation)
    // {
    //     if (empty($models)) return $models;

    //     $firstModel = $models[0];
    //     if (!method_exists($firstModel, $relation)) {
    //         return $models;
    //     }

    //     $relationObj = $firstModel->$relation();
    //     $foreignKey = $relationObj->foreignKey;
    //     $localKey = $relationObj->localKey;
    //     $relationType = get_class($relationObj); // مثلاً HasMany, BelongsTo, HasOne

    //     // جمع‌آوری مقادیر کلید محلی
    //     $keys = array_unique(array_map(function ($m) use ($localKey) {
    //         return $m->{$localKey};
    //     }, $models));

    //     // دریافت داده‌های رابطه
    //     $relatedQuery = $relationObj->getQuery();
    //     $relatedModels = $relatedQuery->whereIn($foreignKey, $keys)->get();

    //     if (strpos($relationType, 'HasMany') !== false) {
    //         // HasMany: گروه‌بندی و ذخیره آرایه
    //         $grouped = [];
    //         foreach ($relatedModels as $related) {
    //             $grouped[$related->{$foreignKey}][] = $related;
    //         }
    //         foreach ($models as $model) {
    //             $keyValue = $model->{$localKey};
    //             $model->relations[$relation] = $grouped[$keyValue] ?? [];
    //         }
    //     } else {
    //         // BelongsTo یا HasOne: نگاشت یک به یک
    //         $mapped = [];
    //         foreach ($relatedModels as $related) {
    //             $mapped[$related->{$foreignKey}] = $related;
    //         }
    //         foreach ($models as $model) {
    //             $keyValue = $model->{$localKey};
    //             $model->relations[$relation] = $mapped[$keyValue] ?? null;
    //         }
    //     }

    //     return $models;
    // }

    protected function eagerLoadRelation($models, $relation, $constraint = null)
    {
        if (empty($models)) return $models;
        $firstModel = $models[0];
        if (!method_exists($firstModel, $relation)) return $models;
        $relationObj = $firstModel->$relation();
        // $foreignKey = $relationObj->foreignKey ?? null;
        // $localKey = $relationObj->localKey ?? 'id';
        $foreignKey = $relationObj->getForeignKey();
        $localKey = $relationObj->getLocalKey();
        $relationType = get_class($relationObj);


        $keys = array_unique(array_map(function ($m) use ($localKey) {
            return $m->{$localKey};
        }, $models));

        $relatedQuery = clone $relationObj->getQuery();

        if ($constraint !== null) {
            $constraint($relatedQuery);
        }

        if (strpos($relationType, 'BelongsToMany') !== false) {
            // برای BelongsToMany، کلید خارجی در جدول pivot است
            // $relatedQuery->whereIn($relationObj->relatedKey, $keys);
            $relatedQuery->whereIn($relationObj->getRelatedKey(), $keys);
            $relatedModels = $relatedQuery->get();
            $relationObj->match($models, $relatedModels, $relation);
        } elseif (strpos($relationType, 'HasMany') !== false) {
            // ====== دیباگ: مطمئن شوید foreignKey خالی نیست ======
            if (empty($foreignKey)) {
                throw new \Exception("ForeignKey is empty for relation: " . $relation);
            }
            // =================================================
            $relatedQuery->whereIn($foreignKey, $keys);
            $relatedModels = $relatedQuery->get();
            $grouped = [];
            foreach ($relatedModels as $related) {
                $grouped[$related->{$foreignKey}][] = $related;
            }
            // foreach ($models as $model) {
            //     $keyValue = $model->{$localKey};
            //     $model->relations[$relation] = new Collection($grouped[$keyValue] ?? []);
            // }
            foreach ($models as $model) {
                $keyValue = $model->{$localKey};
                $model->setRelation($relation, new Collection($grouped[$keyValue] ?? []));
            }
        } else {
            // belongsTo یا hasOne
            $relatedQuery->whereIn($foreignKey, $keys);
            $relatedModels = $relatedQuery->get();
            $mapped = [];
            foreach ($relatedModels as $related) {
                $mapped[$related->{$foreignKey}] = $related;
            }
            foreach ($models as $model) {
                $keyValue = $model->{$localKey};
                // $model->relations[$relation] = $mapped[$keyValue] ?? null;
                $model->setRelation($relation, $mapped[$keyValue] ?? null);
            }
        }
        return $models;
    }

    /**
     * محدود کردن تعداد نتایج (alias limit)
     * @param int $limit
     * @return $this
     */
    public function take($limit)
    {
        return $this->limit($limit);
    }

    /**
     * رد کردن تعداد مشخصی رکورد (alias offset)
     * @param int $offset
     * @return $this
     */
    public function skip($offset)
    {
        return $this->offset($offset);
    }

    public function links($view = null)
    {
        $paginator = $this->paginate(); // فرض کنیم paginate() قبلاً اجرا شده باشد
        return paginate_links($paginator); // از همان تابع کمکی استفاده کن
    }
}

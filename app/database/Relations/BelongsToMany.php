<?php
namespace App\Database\Relations;

use App\Database\Relation;
use App\Database\Builder;
use App\Database\Collection;

class BelongsToMany extends Relation
{
    protected $pivotTable;
    protected $foreignPivotKey;
    protected $relatedPivotKey;
    protected $parentKey;
    protected $relatedKey;
    protected $pivotColumns = [];

    public function __construct($parent, $related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->parentKey = $parentKey;
        $this->relatedKey = $relatedKey;

        $this->query = $related::query();
        if (static::$constraints) {  // <-- استفاده از static::$constraints
            $this->addConstraints();
        }
    }

    protected function addConstraints()
    {
        $keys = $this->getRelatedKeys();
        if (!empty($keys)) {
            $this->query->whereIn($this->relatedKey, $keys);
        } else {
            $this->query->whereRaw('1 = 0'); // هیچ نتیجه‌ای
        }
    }

    protected function getRelatedKeys()
    {
        $pivot = $this->getPivotQuery()
            ->where($this->foreignPivotKey, $this->parent->{$this->parentKey})
            ->get();
        
        // استفاده از pluck روی Collection
        return $pivot->pluck($this->relatedPivotKey);
    }

    protected function getPivotQuery()
    {
        return new Builder($this->pivotTable);
    }

    public function getResults()
    {
        return $this->query->get();
    }

    public function attach($id, array $attributes = [])
    {
        if (is_array($id)) {
            foreach ($id as $relatedId) {
                $this->attach($relatedId, $attributes);
            }
            return;
        }
        $data = [
            $this->foreignPivotKey => $this->parent->{$this->parentKey},
            $this->relatedPivotKey => $id,
        ];
        $data = array_merge($data, $attributes);
        $this->getPivotQuery()->insert($data);
    }

    public function detach($ids = null)
    {
        $query = $this->getPivotQuery()->where($this->foreignPivotKey, $this->parent->{$this->parentKey});
        if ($ids) {
            $ids = is_array($ids) ? $ids : [$ids];
            $query->whereIn($this->relatedPivotKey, $ids);
        }
        return $query->delete();
    }

    public function sync($ids)
    {
        $current = $this->getPivotQuery()
            ->where($this->foreignPivotKey, $this->parent->{$this->parentKey})
            ->get()
            ->pluck($this->relatedPivotKey);
        $ids = array_map('strval', $ids);
        $toAttach = array_diff($ids, $current);
        $toDetach = array_diff($current, $ids);
        if (!empty($toDetach)) $this->detach($toDetach);
        if (!empty($toAttach)) $this->attach($toAttach);
    }

    public function withPivot($columns)
    {
        $this->pivotColumns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = [];
        foreach ($results as $result) {
            $pivot = $result->pivot ?? null;
            $key = $pivot ? $pivot->{$this->foreignPivotKey} : null;
            if ($key) {
                $dictionary[$key][] = $result;
            }
        }
        foreach ($models as $model) {
            $key = $model->{$this->parentKey};
            $model->relations[$relation] = new Collection($dictionary[$key] ?? []);
        }
        return $models;
    }
}
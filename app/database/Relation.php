<?php

namespace App\Database;

abstract class Relation
{
    protected static $constraints = true;
    protected $parent;
    protected $related;
    protected $foreignKey;
    protected $localKey;
    protected $query;

    public function __construct($parent, $related, $foreignKey, $localKey)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->query = $related::query();
    }

    abstract public function getResults();

    public function getQuery()
    {
        return $this->query;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getLocalKey()
    {
        return $this->localKey;
    }

    // برای BelongsToMany که کلید متفاوتی دارد
    public function getRelatedKey()
    {
        return $this->relatedKey ?? null;
    }

    // متدهای زنجیره‌ای را به query منتقل می‌کند
    public function __call($method, $arguments)
    {
        $result = $this->query->$method(...$arguments);
        if ($result === $this->query) {
            return $this;
        }
        return $result;
    }
}

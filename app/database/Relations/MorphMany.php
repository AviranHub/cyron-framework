<?php
namespace App\Database\Relations;

use App\Database\Relation;

class MorphMany extends Relation
{
    protected $morphType;
    protected $morphId;

    public function __construct($parent, $related, $morphType, $morphId, $localKey)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->morphType = $morphType;
        $this->morphId = $morphId;
        $this->localKey = $localKey;

        $this->query = $related::query();
        $this->addConstraints();
    }

    protected function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->morphType, get_class($this->parent))
                        ->where($this->morphId, $this->parent->{$this->localKey});
        }
    }

    public function getResults()
    {
        return $this->query->get();
    }
}
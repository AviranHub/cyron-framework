<?php

namespace Cyron\Database\Relations;

use Cyron\Database\Relation;

class HasMany extends Relation
{
    public function getResults()
    {
        return $this->query->where($this->foreignKey, $this->parent->{$this->localKey})->get();
    }
}
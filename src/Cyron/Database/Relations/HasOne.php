<?php

namespace Cyron\Database\Relations;

use Cyron\Database\Relation;

class HasOne extends Relation
{
    public function getResults()
    {
        return $this->query->where($this->foreignKey, $this->parent->{$this->localKey})->first();
    }
}
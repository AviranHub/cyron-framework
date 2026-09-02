<?php

namespace App\Database\Relations;

use App\Database\Relation;

class HasOne extends Relation
{
    public function getResults()
    {
        return $this->query->where($this->foreignKey, $this->parent->{$this->localKey})->first();
    }
}
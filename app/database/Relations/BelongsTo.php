<?php

namespace App\Database\Relations;

use App\Database\Relation;

class BelongsTo extends Relation
{
    public function getResults()
    {
        return $this->query->where($this->localKey, $this->parent->{$this->foreignKey})->first();
    }
}
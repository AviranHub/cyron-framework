<?php
namespace App\Database\Relations;

use App\Database\Relation;

class MorphTo extends Relation
{
    protected $morphType;
    protected $morphId;

    public function __construct($parent, $morphType, $morphId)
    {
        $this->parent = $parent;
        $this->morphType = $morphType;
        $this->morphId = $morphId;
    }

    public function getResults()
    {
        $type = $this->parent->{$this->morphType};
        $id = $this->parent->{$this->morphId};
        if (!$type || !$id) return null;
        $model = new $type;
        return $model::find($id);
    }

    public function associate($model)
    {
        $this->parent->{$this->morphType} = get_class($model);
        $this->parent->{$this->morphId} = $model->id;
        return $this->parent;
    }
}
<?php
namespace App\Database\Relations;

use App\Database\Relation;

class MorphToMany extends BelongsToMany
{
    protected $morphType;
    protected $morphClass;

    public function __construct($parent, $related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null)
    {
        $this->morphType = $name . '_type';
        $this->morphClass = get_class($parent);
        $table = $table ?: $this->getMorphTable($name);
        $foreignPivotKey = $foreignPivotKey ?: $name . '_id';
        $relatedPivotKey = $relatedPivotKey ?: $related::getTable() . '_id';
        parent::__construct($parent, $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey);
    }

    protected function getMorphTable($name)
    {
        return $name . 's'; // معمولاً 'taggables'
    }

    protected function addConstraints()
    {
        parent::addConstraints();
        $this->query->where($this->morphType, $this->morphClass);
    }
}
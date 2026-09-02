<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;
use App\Database\Db;

class Unique implements Rule
{
    private function identifier($name)
    {
        if (!is_string($name) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) throw new \InvalidArgumentException('Invalid database identifier.');
        return '`' . $name . '`';
    }
    protected $table;
    protected $column;
    protected $except;

    public function __construct($table, $column, $except = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->except = $except;
    }

    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true;
        $table = $this->identifier($this->table);
        $column = $this->identifier($this->column);
        $db = Db::getInstance();
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$value];
        if ($this->except) {
            $sql .= " AND id != ?";
            $params[] = $this->except;
        }
        $stmt = $db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count == 0;
    }

    public function message($field)
    {
        return "مقدار فیلد {$field} قبلاً ثبت شده است.";
    }
}
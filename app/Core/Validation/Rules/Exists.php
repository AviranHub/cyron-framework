<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;
use App\Database\Db;

class Exists implements Rule
{
    private function identifier($name)
    {
        if (!is_string($name) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) throw new \InvalidArgumentException('Invalid database identifier.');
        return '`' . $name . '`';
    }
    protected $table;
    protected $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function passes($fieldName, $value, $data)
    {
        if (is_null($value) || $value === '') return true; // nullable

        $table = $this->identifier($this->table);
        $column = $this->identifier($this->column);
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    public function message($field)
    {
        return "مقدار انتخاب شده برای فیلد {$field} معتبر نیست.";
    }
}
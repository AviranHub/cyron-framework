<?php
namespace App\Database;

final class SqlGuard
{
    public static function identifier(string $name): string
    {
        $name = trim($name);
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\\.[A-Za-z_][A-Za-z0-9_]*)?$/', $name)) throw new \InvalidArgumentException('Invalid SQL identifier.');
        return implode('.', array_map(static fn($part) => '`'.$part.'`', explode('.', $name)));
    }
    public static function direction(string $direction): string
    {
        $direction = strtoupper(trim($direction));
        if (!in_array($direction, ['ASC','DESC'], true)) throw new \InvalidArgumentException('Invalid SQL order direction.');
        return $direction;
    }
    public static function operator(string $operator): string
    {
        $operator = strtoupper(trim($operator));
        $allowed = ['=','!=','<>','<','<=','>','>=','LIKE'];
        if (!in_array($operator, $allowed, true)) throw new \InvalidArgumentException('Invalid SQL operator.');
        return $operator;
    }
}

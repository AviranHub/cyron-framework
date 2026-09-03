<?php

namespace App\Database;

class TableBuilder
{
    protected array $columns = [];
    protected array $indexes = [];
    protected array $foreignKeys = [];
    protected string $table;
    protected string $engine = 'InnoDB';
    protected string $charset = 'utf8mb4';
    protected ?string $lastColumnName = null;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[$name] = "`{$name}` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY";
        $this->lastColumnName = $name;
        return $this;
    }

    public function increments(string $name): self
    {
        $this->columns[$name] = "`{$name}` INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
        $this->lastColumnName = $name;
        return $this;
    }

    public function string(string $name, int $length = 255, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` VARCHAR({$length}) {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function text(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` TEXT {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function longText(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` LONGTEXT {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function integer(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` INT {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function bigInteger(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` BIGINT {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function tinyInteger(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` TINYINT {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function decimal(string $name, int $total = 8, int $places = 2, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` DECIMAL({$total},{$places}) {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function boolean(string $name, bool $default = false): self
    {
        $defaultValue = $default ? '1' : '0';
        $this->columns[$name] = "`{$name}` TINYINT(1) NOT NULL DEFAULT '{$defaultValue}'";
        $this->lastColumnName = $name;
        return $this;
    }

    public function enum(string $name, array $values, ?string $default = null): self
    {
        $enumValues = "'" . implode("','", $values) . "'";
        $defaultStr = $default ? " DEFAULT '{$default}'" : '';
        $this->columns[$name] = "`{$name}` ENUM({$enumValues}) NOT NULL{$defaultStr}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function json(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` JSON {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function date(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` DATE {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function dateTime(string $name, bool $nullable = false): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` DATETIME {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function timestamp(string $name, bool $nullable = true): self
    {
        $null = $nullable ? 'NULL' : 'NOT NULL';
        $this->columns[$name] = "`{$name}` TIMESTAMP {$null}";
        $this->lastColumnName = $name;
        return $this;
    }

    public function nullable(): self
    {
        if ($this->lastColumnName !== null) {
            $this->columns[$this->lastColumnName] = str_replace('NOT NULL', 'NULL', $this->columns[$this->lastColumnName]);
        }
        return $this;
    }

    public function default(string $value): self
    {
        if ($this->lastColumnName === null) {
            return $this;
        }

        $sqlFunctions = [
            'CURRENT_TIMESTAMP', 'NOW', 'CURRENT_DATE', 'CURRENT_TIME',
            'LOCALTIMESTAMP', 'LOCALTIME', 'UTC_TIMESTAMP', 'UTC_DATE',
            'UTC_TIME', 'SYSDATE', 'UUID', 'UUID_TO_BIN', 'CURRENT_USER',
            'SESSION_USER', 'SYSTEM_USER'
        ];

        $upper = strtoupper(trim($value));

        if (!is_numeric($value) && is_string($value) && !str_contains($value, '_') && !in_array($upper, $sqlFunctions)) {
            $value = "'" . $value . "'";
        }

        $this->columns[$this->lastColumnName] .= " DEFAULT {$value}";
        return $this;
    }

    public function unsigned(): self
    {
        if ($this->lastColumnName !== null) {
            $this->columns[$this->lastColumnName] = preg_replace('/\b(INT|BIGINT|TINYINT|DECIMAL)\b/', '$1 UNSIGNED', $this->columns[$this->lastColumnName]);
        }
        return $this;
    }

    public function primary(): self
    {
        if ($this->lastColumnName !== null) {
            $this->columns[$this->lastColumnName] .= " PRIMARY KEY";
        }
        return $this;
    }

    public function useCurrent(): self
    {
        if ($this->lastColumnName !== null && strpos($this->columns[$this->lastColumnName], 'DEFAULT') === false) {
            $this->columns[$this->lastColumnName] .= " DEFAULT CURRENT_TIMESTAMP";
        }
        return $this;
    }

    public function unique(): self
    {
        if ($this->lastColumnName !== null) {
            $this->indexes[] = "UNIQUE KEY `{$this->lastColumnName}_unique` (`{$this->lastColumnName}`)";
        }
        return $this;
    }

    public function index($columns, ?string $name = null): self
    {
        if (is_array($columns)) {
            $indexName = $name ?? $this->generateIndexName($columns);
            $columnsList = implode('`, `', $columns);
            $this->indexes[] = "KEY `{$indexName}` (`{$columnsList}`)";
        } else {
            $col = $columns;
            $indexName = $name ?? "{$col}_index";
            $this->indexes[] = "KEY `{$indexName}` (`{$col}`)";
        }
        return $this;
    }

    public function foreign(string $column): self
    {
        $this->foreignKeys[] = ['column' => $column, 'name' => "{$this->table}_{$column}_foreign"];
        return $this;
    }

    public function references(string $column): self
    {
        $i = array_key_last($this->foreignKeys);
        if ($i === null) throw new \RuntimeException('No foreign key defined.');
        $this->foreignKeys[$i]['references'] = $column;
        return $this;
    }

    public function on(string $table): self
    {
        $i = array_key_last($this->foreignKeys);
        if ($i === null) throw new \RuntimeException('No foreign key defined.');
        $this->foreignKeys[$i]['on'] = $table;
        $this->foreignKeys[$i]['onDelete'] = 'RESTRICT';
        return $this;
    }

    public function onDelete(string $action): self
    {
        $i = array_key_last($this->foreignKeys);
        if ($i === null) throw new \RuntimeException('No foreign key defined.');
        $this->foreignKeys[$i]['onDelete'] = strtoupper($action);
        return $this;
    }

    protected function buildForeignKeys(): array
    {
        $sql = [];
        foreach ($this->foreignKeys as $foreignKey) {
            if (!isset($foreignKey['column'], $foreignKey['references'], $foreignKey['on'])) continue;
            $column = $foreignKey['column'];
            $references = $foreignKey['references'];
            $on = $foreignKey['on'];
            $onDelete = $foreignKey['onDelete'] ?? 'RESTRICT';
            $name = $foreignKey['name'] ?? "{$this->table}_{$column}_foreign";
            $sql[] = "CONSTRAINT `{$name}` FOREIGN KEY (`{$column}`) REFERENCES `{$on}`(`{$references}`) ON DELETE {$onDelete}";
        }
        return $sql;
    }

    public function foreignId(string $name): self
    {
        $this->columns[$name] = "`{$name}` BIGINT UNSIGNED NOT NULL";
        $this->lastColumnName = $name;
        return $this;
    }

    public function constrained(?string $table = null): self
    {
        $column = $this->lastColumnName;

        if ($column === null) {
            throw new \RuntimeException('No column defined to apply constrained() on.');
        }

        if ($table === null) {
            $base = preg_replace('/_id$/', '', $column);

            if (preg_match('/y$/i', $base) && !preg_match('/[aeiou]y$/i', $base)) {
                $table = substr($base, 0, -1) . 'ies';
            } elseif (preg_match('/(s|x|z|ch|sh)$/i', $base)) {
                $table = $base . 'es';
            } else {
                $table = $base . 's';
            }
        }

        $this->foreignKeys[] = [
            'column' => $column,
            'name' => "{$this->table}_{$column}_foreign",
            'references' => 'id',
            'on' => $table,
            'onDelete' => 'RESTRICT',
        ];

        return $this;
    }

    public function timestamps(bool $withDeletedAt = false): self
    {
        $this->timestamp('created_at', true)->default('CURRENT_TIMESTAMP');
        $this->timestamp('updated_at', true)->default('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        if ($withDeletedAt) {
            $this->timestamp('deleted_at', true);
        }
        return $this;
    }

    public function softDeletes(): self
    {
        $this->timestamp('deleted_at', true);
        return $this;
    }

    public function build(): string
    {
        $columnsSql = implode(",\n    ", $this->columns);
        $indexesSql = !empty($this->indexes) ? ",\n    " . implode(",\n    ", $this->indexes) : '';
        $foreignSql = '';
        $foreignKeys = $this->buildForeignKeys();
        if (!empty($foreignKeys)) {
            $foreignSql = ",\n    " . implode(",\n    ", $foreignKeys);
        }

        return "CREATE TABLE `{$this->table}` (\n    {$columnsSql}{$indexesSql}{$foreignSql}\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset}";
    }

    protected function generateIndexName(array $columns, string $suffix = 'index'): string
    {
        return implode('_', $columns) . "_{$suffix}";
    }
}

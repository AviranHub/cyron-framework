<?php
namespace App\Database;

use IteratorAggregate;
use ArrayIterator;
use Countable;
use ArrayAccess;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    protected array $items = [];
    
    public function __construct(array $items = []) {
        $this->items = array_values($items);
    }
    
    public function take(int $limit): self {
        return new static(array_slice($this->items, 0, $limit));
    }
    
    public function count(): int {
        return count($this->items);
    }

    public function pluck(string $key): array {
        return array_map(function($item) use ($key) {
            return is_array($item) ? ($item[$key] ?? null) : ($item->{$key} ?? null);
        }, $this->items);
    }

    public function map(callable $callback): self {
        return new static(array_map($callback, $this->items));
    }

    public function first() {
        return $this->items[0] ?? null;
    }
    
    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->items);
    }
    
    public function offsetExists(mixed $offset): bool {
        return isset($this->items[$offset]);
    }
    
    public function offsetGet(mixed $offset): mixed {
        return $this->items[$offset] ?? null;
    }
    
    public function offsetSet(mixed $offset, mixed $value): void {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    public function offsetUnset(mixed $offset): void {
        unset($this->items[$offset]);
    }
    
    public function toArray(): array {
        return $this->items;
    }
}
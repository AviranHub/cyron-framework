<?php

namespace App\Database;

use IteratorAggregate;
use ArrayIterator;

class Paginator implements IteratorAggregate
{
    protected $items;           // می‌تواند Collection یا array باشد
    protected int $currentPage;
    protected int $perPage;
    protected int $total;
    protected int $lastPage;
    protected string $pageName;

    public function __construct(array $paginationData)
    {
        $this->items = $paginationData['data'];        // Collection یا array
        $this->currentPage = $paginationData['current_page'];
        $this->perPage = $paginationData['per_page'];
        $this->total = $paginationData['total'];
        $this->lastPage = $paginationData['last_page'];
        $this->pageName = $paginationData['page_name'] ?? 'page';
    }

    // دسترسی به آیتم‌ها
    public function items()
    {
        return $this->items;
    }

    // تولید لینک‌های صفحه‌بندی (با استفاده از تابع کمکی)
    public function links($view = null)
    {
        return paginate_links([
            'data'         => $this->items,
            'current_page' => $this->currentPage,
            'per_page'     => $this->perPage,
            'total'        => $this->total,
            'last_page'    => $this->lastPage,
            'page_name'    => $this->pageName,
            'has_pages'    => $this->lastPage > 1,
            'has_prev'     => $this->currentPage > 1,
            'has_next'     => $this->currentPage < $this->lastPage,
            'prev_page'    => $this->currentPage > 1 ? $this->currentPage - 1 : null,
            'next_page'    => $this->currentPage < $this->lastPage ? $this->currentPage + 1 : null,
        ]);
    }

    // اجازه می‌دهد مستقیماً روی $books حلقه بزنیم
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    // دسترسی به پراپرتی‌های خاص (مثلاً $books->total)
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        return null;
    }
}
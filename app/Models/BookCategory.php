<?php

namespace App\Models;

use App\Database\Model;

class BookCategory extends Model
{
    protected static $table = 'book_categories';
    protected static array $fillable = ['name', 'icon'];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id', 'id');
    }
}

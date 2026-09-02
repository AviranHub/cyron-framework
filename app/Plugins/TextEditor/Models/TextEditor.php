<?php
namespace Plugins\TextEditor\Models;

use App\Database\Model;

class TextEditor extends Model
{
    protected static $table = 'texteditors';
    protected static array $fillable = ['title', 'content'];
}
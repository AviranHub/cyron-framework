<?php
namespace Plugins\TextEditor\Models;

use Cyron\Database\Model;

class TextEditor extends Model
{
    protected static $table = 'texteditors';
    protected static array $fillable = ['title', 'content'];
}
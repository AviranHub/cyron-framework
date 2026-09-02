<?php
namespace Plugins\TextEditor\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;

class create_TextEditor_tables
{
    public static function up()
    {
        Schema::create('texteditors', function (TableBuilder $table) {
            $table->id();
            $table->string('title', 191);
            $table->text('content')->nullable();
            $table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('texteditors');
    }
}
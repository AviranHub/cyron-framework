<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('book_categories', function (TableBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('book_categories');
    }
};

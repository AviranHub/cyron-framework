<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('book_parts', function (TableBuilder $table) {
            $table->id();
            $table->longtext('text');
            $table->integer('page_id');
            $table->string('page_name');
            $table->integer('book_id');
            $table->integer('publisher_id');
            $table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('book_categorys');
    }
};

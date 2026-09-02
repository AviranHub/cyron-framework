<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('tags', function (TableBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('article'); // 'article', 'book', 'general'
            $table->text('description')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index('type');
            $table->index('usage_count');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('tags');
    }
};
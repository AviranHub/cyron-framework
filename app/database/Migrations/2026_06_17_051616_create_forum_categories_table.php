<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('forum_categories', function (TableBuilder $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description', true); // nullable
            $table->integer('sort_order')->default('0');
            $table->timestamps(true); // created_at, updated_at, deleted_at
            
            // ایندکس‌ها
            $table->index('sort_order');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('forum_categories');
    }
};
<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('forum_topics', function (TableBuilder $table) {
            $table->id();
            
            // کلیدهای خارجی
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            
            // فیلدهای اصلی
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->longText('content');
            $table->integer('views')->default('0');
            
            // وضعیت‌ها
            $table->boolean('is_pinned');
            $table->boolean('is_locked');
            
            $table->timestamps(true); // created_at, updated_at, deleted_at
            
            // کلیدهای خارجی
            $table->foreign('category_id')
                  ->references('id')
                  ->on('forum_categories')
                  ->onDelete('CASCADE');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('CASCADE');
            
            // ایندکس‌ها
            $table->index('created_at');
            $table->index('is_pinned');
            $table->index('is_locked');
            $table->index('category_id');
            $table->index('user_id');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('forum_topics');
    }
};
<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('forum_posts', function (TableBuilder $table) {
            $table->id();
            
            // کلیدهای خارجی
            $table->bigInteger('topic_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('parent_post_id')->unsigned()->nullable();
            
            // محتوا
            $table->longText('content');
            
            // امتیازات
            $table->integer('likes')->default('0');
            $table->integer('dislikes')->default('0');
            
            $table->timestamps(true); // created_at, updated_at, deleted_at
            
            // کلیدهای خارجی
            $table->foreign('topic_id')
                  ->references('id')
                  ->on('forum_topics')
                  ->onDelete('CASCADE');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('CASCADE');
                  
            $table->foreign('parent_post_id')
                  ->references('id')
                  ->on('forum_posts')
                  ->onDelete('SET NULL');
            
            // ایندکس‌ها
            $table->index('created_at');
            $table->index('topic_id');
            $table->index('user_id');
            $table->index('parent_post_id');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('forum_posts');
    }
};
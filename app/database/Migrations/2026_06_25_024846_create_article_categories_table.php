<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('article_categories', function (TableBuilder $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);

            // دسته‌بندی سلسله‌مراتبی
            $table->foreignId('parent_id')->nullable()->constrained('article_categories')->nullOnDelete();

            // آمار
            $table->integer('articles_count')->default(0);

            // وضعیت
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // سئو
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();

            // تنظیمات
            $table->json('settings')->nullable();

            $table->timestamps();

            // ایندکس‌ها
            $table->index('slug');
            $table->index('parent_id');
            $table->index('order');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('articles_count');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('article_categories');
    }
};
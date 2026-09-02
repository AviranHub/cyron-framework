<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('views', function (TableBuilder $table) {
            // کلید اصلی
            $table->id();
            
            // // رشته‌ها
            // $table->string('name', 191);
            // $table->string('slug', 191)->unique();
            // $table->string('email', 191)->nullable();
            
            // // متن
            // $table->text('description', true); // nullable
            // $table->longText('content');
            
            // // اعداد
            // $table->integer('price')->default('0');
            // $table->bigInteger('views')->default('0');
            // $table->tinyInteger('status')->default('1');
            // $table->decimal('rating', 3, 2)->default('0.00');
            
            // // بولین
            // $table->boolean('is_active')->default(true);
            
            // // Enum
            // $table->enum('role', ['user', 'admin', 'moderator'], 'user');
            
            // // تاریخ و زمان
            // $table->date('published_date')->nullable();
            // $table->dateTime('last_seen_at')->nullable();
            // $table->timestamp('verified_at')->nullable();
            
            // // کلید خارجی
            // $table->bigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            
            // $table->bigInteger('category_id');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('SET NULL');
            
            // // ایندکس‌ها
            // $table->index('status');
            // $table->index('created_at');
            
            // تایم‌استمپ‌ها (با softDeletes)
            $table->timestamps(true); // created_at, updated_at, deleted_at
            
            // اگر softDeletes جدا می‌خواهید:
            // $table->softDeletes();
        });
    }

    public static function down()
    {
        Schema::dropIfExists('views');
    }
};
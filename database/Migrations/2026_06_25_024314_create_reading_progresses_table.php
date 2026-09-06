<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('reading_progresses', function (TableBuilder $table) {
            // کلید اصلی
            $table->id();

            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('book_id')->unsigned();
            $table->integer('last_page')->default(1);
            
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
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->onDelete('CASCADE');

            $table->index(['user_id', 'book_id']);
            
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
        Schema::dropIfExists('reading_progresses');
    }
};
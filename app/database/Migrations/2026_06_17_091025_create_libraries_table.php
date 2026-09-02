<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('libraries', function (TableBuilder $table) {
            // ========== کلید اصلی ==========
            $table->id();

            // ========== کلیدهای خارجی ==========
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('book_id')->unsigned();

            // ========== فیلدهای دیگر ==========
            $table->integer('shelf_id')->nullable();
            $table->timestamp('purchased_at')->useCurrent();

            // ========== زمان‌ها ==========
            $table->timestamps(false); // فقط created_at و updated_at

            // ========== کلیدهای خارجی ==========
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('CASCADE');

            $table->foreign('book_id')
                  ->references('id')
                  ->on('books')
                  ->onDelete('CASCADE');

            // ========== ایندکس یکتا ==========
            // برای جلوگیری از خرید تکراری یک کتاب توسط یک کاربر
            // در TableBuilder فعلاً unique روی ستون‌ها به این صورت اعمال می‌شود:
            $table->index(['user_id', 'book_id']); // ایندکس ترکیبی
            // توجه: برای unique باید متد جداگانه‌ای در TableBuilder اضافه شود
        });
    }

    public static function down()
    {
        Schema::dropIfExists('libraries');
    }
};

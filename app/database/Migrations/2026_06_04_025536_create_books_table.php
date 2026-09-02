<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public function up()
    {
        Schema::create('books', function (TableBuilder $table) {
            // ========== کلید اصلی ==========
            $table->id();

            // ========== اطلاعات پایه ==========
            $table->string('title', 191);
            $table->string('slug', 191)->unique();
            $table->text('subtitle', true); // nullable
            $table->text('description', true);
            $table->text('introduction', true);
            $table->json('contents')->nullable();
            $table->json('details')->nullable();

            // ========== نویسنده و ناشر ==========
            $table->bigInteger('author_id')->unsigned()->nullable();
            $table->string('author_name', 191)->nullable();
            $table->text('author_avatar', true);
            $table->bigInteger('publisher_id')->unsigned()->nullable();
            $table->string('publisher', 191)->nullable();

            // ========== فایل‌ها و رسانه ==========
            $table->text('cover'); // NOT NULL
            $table->text('pdf', true);

            // ========== پرچم‌ها ==========
            $table->boolean('is_audio');
            $table->boolean('is_buy');
            $table->boolean('is_show');
            $table->boolean('is_download');
            $table->boolean('is_subscribe');
            $table->boolean('is_read');
            $table->boolean('is_public');
            $table->boolean('is_bestseller');

            // ========== دسته‌بندی ==========
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('genre_id')->unsigned()->nullable();
            $table->enum('access_type', ['all', 'free', 'premium', 'subscription', 'purchase'])->default('all');
            $table->string('status', 191)->default('published');
            $table->string('language', 191)->default('fa');

            // ========== فیلدهای عددی ==========
            $table->integer('pages')->default('0');
            $table->integer('pages_count')->default('0');
            $table->decimal('price', 15, 2)->default('0.00');
            $table->integer('copen')->default('0');
            $table->integer('views')->default('0');
            $table->integer('likes')->default('0');

            // ========== تاریخ‌ها ==========
            $table->date('published_date')->nullable();
            $table->string('conditions', 191)->nullable();

            // ========== داده‌های اضافی ==========
            $table->json('others')->nullable();

            // ========== زمان‌ها ==========
            $table->timestamps(true); // فقط created_at و updated_at

            // ========== کلیدهای خارجی ==========
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('SET NULL');

            $table->foreign('publisher_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('SET NULL');

            $table->foreign('category_id')
                  ->references('id')
                  ->on('book_categories')
                  ->onDelete('CASCADE');

            $table->foreign('genre_id')
                  ->references('id')
                  ->on('genres')
                  ->onDelete('SET NULL');

            // ========== ایندکس‌ها ==========
            $table->index('slug');
            $table->index('author_id');
            $table->index('category_id');
            $table->index('genre_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('is_bestseller');
            $table->index('price');
            $table->index(['author_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['published_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};
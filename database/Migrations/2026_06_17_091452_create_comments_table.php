<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('comments', function (TableBuilder $table) {
            // ========== کلید اصلی ==========
            $table->id();

            // ========== نویسنده ==========
            $table->bigInteger('author_id')->unsigned();
            $table->string('author_name', 191);

            // ========== محتوای نظر ==========
            $table->text('text');

            // ========== پاسخ به نظر دیگر ==========
            $table->bigInteger('reply_id')->unsigned()->nullable();
            $table->integer('depth')->default('0');

            // ========== وضعیت‌ها ==========
            $table->boolean('is_public');
            $table->boolean('is_admin_view');
            $table->boolean('is_publisher_view');
            $table->boolean('is_approved');
            $table->timestamp('approved_at')->nullable();

            // ========== آمار ==========
            $table->integer('replies_count')->default('0');
            $table->integer('report_count')->default('0');

            // ========== ویرایش ==========
            $table->boolean('is_edited');
            $table->timestamp('edited_at')->nullable();

            // ========== والد (مورف) ==========
            $table->string('commentable_type', 191);
            $table->bigInteger('commentable_id')->unsigned();

            // ========== زمان‌ها (با soft delete) ==========
            $table->timestamps(true); // created_at, updated_at, deleted_at

            // ========== کلیدهای خارجی ==========
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('CASCADE');

            $table->foreign('reply_id')
                  ->references('id')
                  ->on('comments')
                  ->onDelete('CASCADE');

            // ========== ایندکس‌ها ==========
            $table->index(['author_id', 'created_at']);
            $table->index(['commentable_type', 'commentable_id', 'is_approved']);
            $table->index(['is_public', 'is_approved']);
            $table->index('reply_id');
            $table->index('created_at');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('comments');
    }
};
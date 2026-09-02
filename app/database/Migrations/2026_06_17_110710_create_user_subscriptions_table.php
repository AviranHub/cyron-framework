<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('user_subscriptions', function (TableBuilder $table) {
            // ========== کلید اصلی ==========
            $table->id();

            // ========== کلیدهای خارجی ==========
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('subscription_id')->unsigned();

            // ========== تاریخ‌ها ==========
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            // ========== وضعیت ==========
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');

            // ========== فیلدهای اضافی ==========
            $table->boolean('trial_used');
            $table->enum('payment_method', ['wallet', 'gateway', 'free'])->default('gateway');
            $table->string('transaction_id', 191)->nullable();
            $table->integer('books_downloaded_this_month')->default('0');
            $table->timestamp('last_download_reset')->nullable();
            $table->json('others')->nullable();

            // ========== زمان‌ها ==========
            $table->timestamps(false);

            // ========== کلیدهای خارجی ==========
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('CASCADE');

            $table->foreign('subscription_id')
                  ->references('id')
                  ->on('subscriptions')
                  ->onDelete('CASCADE');

            // ========== ایندکس‌ها ==========
            $table->index(['user_id', 'subscription_id']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'end_date']);
            $table->index('end_date');
            $table->index('start_date');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
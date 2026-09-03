<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('transactions', function (TableBuilder $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['charge', 'purchase', 'subscription', 'refund']);
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled', 'refunded']);
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable()->unique();
            $table->string('tracking_code')->nullable();
            $table->enum('payment_method', ['wallet', 'zarinpal', 'other_gateway']);
            $table->string('gateway_name')->nullable();
            $table->string('authority')->nullable();
            $table->decimal('wallet_amount', 15, 2)->default(0);
            $table->json('gateway_response')->nullable();
            $table->json('others')->nullable();
            $table->string('payable_type')->nullable();
            $table->bigInteger('payable_id')->unsigned()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps(true);

            $table->index(['payable_type', 'payable_id']);
            $table->index(['user_id', 'status']);
            $table->index('reference_id');
            $table->index('authority');
            $table->index('created_at');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('transactions');
    }
};
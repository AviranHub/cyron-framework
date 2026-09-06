<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('wallet_transactions', function (TableBuilder $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'withdraw', 'refund']);
            $table->string('description');
            $table->foreignId('transaction_id')->nullable()->constrained();
			$table->json('others')->nullable();

            $table->timestamps(true);

            // Indexes
            $table->index('user_id');
            $table->index('wallet_id');
            $table->index('transaction_id');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
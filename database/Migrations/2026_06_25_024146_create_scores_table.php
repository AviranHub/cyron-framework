<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('scores', function (TableBuilder $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->string('source_type')->nullable(); // book, subscription, etc.
            $table->foreignId('source_id')->nullable(); // book_id, subscription_id
            $table->string('description');
            $table->timestamp('expires_at')->nullable();
			$table->json('others')->nullable();

            $table->timestamps(true);

            // Indexes
            $table->index('user_id');
            $table->index(['source_type', 'source_id']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('scores');
    }
};
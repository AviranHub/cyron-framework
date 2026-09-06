<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('discounts', function (TableBuilder $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 64)->nullable()->unique();
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 15, 2);
            $table->enum('applies_to', ['all', 'book', 'subscription'])->default('all');
            $table->enum('target_type', ['all', 'book', 'category', 'author', 'subscription'])->default('all');
            $table->bigInteger('target_id')->unsigned()->nullable();
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active', true);
            $table->timestamps(true);
            $table->index(['applies_to', 'target_type', 'target_id']);
            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('discounts');
    }
};

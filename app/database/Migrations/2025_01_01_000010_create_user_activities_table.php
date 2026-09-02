<?php
// app/Modules/Auth/Migrations/2025_01_01_000000_create_users_table.php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('user_activities', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('category', ['authentication', 'browsing', 'purchase', 'content_interaction', 'profile', 'system'])->default('browsing');
            $table->string('action', 191);
            $table->string('subject_type')->nullable();
            $table->bigInteger('subject_id')->unsigned()->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->json('data')->nullable();
            $table->timestamps(true);
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->index(['user_id', 'occurred_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('user_activities');
    }
};

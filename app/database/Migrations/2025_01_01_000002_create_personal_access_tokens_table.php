<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('personal_access_tokens', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('token', 80)->unique();
            $table->string('refresh_token', 80)->nullable()->unique(); // ← جدید
            $table->string('name', 255)->nullable();
            $table->enum('type', ['access', 'refresh'])->default('access'); // ← جدید
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('refresh_expires_at')->nullable(); // ← جدید
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable(); // ← جدید (ابطال نرم)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'token']);
            $table->index(['refresh_token']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
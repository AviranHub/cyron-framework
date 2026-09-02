<?php
// app/Modules/Auth/Migrations/2025_01_01_000000_create_users_table.php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('users', function (TableBuilder $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password', 191);
            $table->string('role', 50)->default('user');
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps(true);
            $table->softDeletes();
            
            $table->index('email');
            $table->index('phone');
            $table->index('role');
        });
        
        // Schema::create('otp_codes', function (TableBuilder $table) {
        //     $table->id();
        //     $table->integer('code');
        //     $table->string('phone', 11);
        //     $table->timestamps(true);
        //     $table->softDeletes();
        // });
    }

    public static function down()
    {
        Schema::dropIfExists('users');
    }
};
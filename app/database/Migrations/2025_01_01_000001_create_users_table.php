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
            // ========== فیلدهای اصلی ==========
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('photo', true);      // nullable
            $table->string('avatar')->default('user-avatar.png');
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            
            // ========== تاییدها ==========
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            // ========== احراز هویت ==========
            $table->string('password');
            $table->string('remember_token', 150)->nullable();
            
            // ========== اطلاعات کاربر ==========
            $table->text('bio', true);        // nullable
            $table->integer('login_count')->default('0');
            $table->timestamp('last_login_at')->nullable();
            $table->json('settings')->nullable();
            
            // ========== وضعیت ==========
            $table->enum('status', ['active', 'inactive', 'suspended', 'banned'])->default('active');
            $table->timestamp('suspended_until')->nullable();
            
            // ========== RBAC ==========
            $table->bigInteger('primary_role_id')->unsigned()->nullable();
            
            // ========== زمان‌ها (با soft delete) ==========
            $table->timestamps(true); // created_at, updated_at, deleted_at
            
            // ========== کلید خارجی ==========
            $table->foreign('primary_role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('SET NULL');
            
            // ========== ایندکس‌ها ==========
            $table->index('status');
            $table->index('last_login_at');
            $table->index('created_at');
            $table->index('email');
            $table->index('phone');
            $table->index('username');
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
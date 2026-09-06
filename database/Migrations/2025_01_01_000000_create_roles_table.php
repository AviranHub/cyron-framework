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
        Schema::create('roles', function (TableBuilder $table) {
            // ========== فیلدهای اصلی ==========
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('description', true); // nullable
            $table->boolean('is_system',false);
            $table->boolean('is_active',true);
            $table->integer('priority')->default('0');
            $table->json('others')->nullable(); // ✅ اضافه شد
            
            // ========== زمان‌ها ==========
            $table->timestamps(false); // فقط created_at و updated_at (بدون deleted_at)
            
            // ========== ایندکس‌ها ==========
            $table->index('slug');
            $table->index('is_system');
            $table->index('is_active');
            $table->index('priority');
            $table->index(['is_active', 'priority']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('roles');
    }
};

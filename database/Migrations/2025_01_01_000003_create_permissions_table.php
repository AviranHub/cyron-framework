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
        Schema::create('permissions', function (TableBuilder $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->string('group', 191);
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_critical')->default(0);
            $table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('permissions');
    }
};

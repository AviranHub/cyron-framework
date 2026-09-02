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
        Schema::create('user_roles', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('role_id')->unsigned();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps(true);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('CASCADE');
            $table->unique(['user_id', 'role_id']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('user_roles');
    }
};

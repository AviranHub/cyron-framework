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
        Schema::create('role_permissions', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->timestamps(true);

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('CASCADE');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('CASCADE');
            $table->unique(['role_id', 'permission_id']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('role_permissions');
    }
};

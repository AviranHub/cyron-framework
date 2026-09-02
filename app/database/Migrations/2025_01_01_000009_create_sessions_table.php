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
        Schema::create('sessions', function (TableBuilder $table) {
            $table->string('id', 191)->primary();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity');
            $table->index('user_id');
            $table->index('last_activity');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('sessions');
    }
};

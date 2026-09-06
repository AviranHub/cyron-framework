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
        Schema::create('phone_verification_codes', function (TableBuilder $table) {
            $table->id();
            $table->string('phone', 20);
            $table->string('code', 6);
            $table->timestamp('expires_at');
            $table->timestamps(true);
            $table->index('phone');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('phone_verification_codes');
    }
};

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
        Schema::create('user_otps', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('otp_token', 191);
            $table->string('otp_type', 191)->default('login');
            $table->timestamp('otp_time')->useCurrent();
            $table->string('otp_ip', 45);
            $table->string('otp_code', 10);
            $table->timestamps(true);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->index('otp_token');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('user_otps');
    }
};

<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('remember_tokens', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('selector', 32)->unique();
            $table->string('token_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->index('user_id');
            $table->index('expires_at');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('remember_tokens');
    }
};

<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('author_follows', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('author_name', 191);
            $table->timestamps();
            $table->unique(['user_id', 'author_name']);
            $table->index('author_name');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('author_follows');
    }
};

<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('author_supports', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('author_name', 191);
            $table->decimal('amount', 20, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'], 'pending');
            $table->string('message', 500, true);
            $table->timestamps();
            $table->index(['author_name', 'status']);
            $table->index('user_id');
        });
    }

    public static function down()
    {
        Schema::dropIfExists('author_supports');
    }
};

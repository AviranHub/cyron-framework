<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('wallets', function (TableBuilder $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->unique();
            $table->decimal('balance', 15, 2)->default(0);
			$table->json('others')->nullable();
            $table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('wallets');
    }
};
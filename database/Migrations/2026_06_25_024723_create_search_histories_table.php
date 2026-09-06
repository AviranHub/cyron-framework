<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('search_histories', function (TableBuilder $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('query');
            $table->json('filters')->nullable();
			$table->json('others')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('search_histories');
    }
};
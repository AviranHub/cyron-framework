<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration {
    public static function up()
    {
        Schema::create('jobs', function (TableBuilder $table) {
            $table->id();
            $table->string('queue', 100)->index();
            $table->longText('payload');
            $table->integer('attempts')->default(0);
            $table->integer('available_at')->index();
            $table->integer('reserved_at', true)->nullable();
            $table->integer('failed_at', true)->nullable();
            $table->text('last_error', true)->nullable();
            $table->integer('created_at')->index();
        });
    }

    public static function down()
    {
        Schema::dropIfExists('jobs');
    }
};

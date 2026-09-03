<?php
namespace App\Database\Migrations;
use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration {
    public static function up() {
        Schema::create('user_two_factors', function (TableBuilder $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->string('channel', 20);
            $table->string('target', 191);
            $table->timestamp('enabled_at');
            $table->timestamp('disabled_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public static function down() { Schema::dropIfExists('user_two_factors'); }
};

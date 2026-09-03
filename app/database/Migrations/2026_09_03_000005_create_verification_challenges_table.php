<?php
namespace App\Database\Migrations;
use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration {
    public static function up() {
        Schema::create('verification_challenges', function (TableBuilder $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('channel', 20)->index();
            $table->string('target', 191);
            $table->string('code_hash', 64);
            $table->string('purpose', 64)->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public static function down() { Schema::dropIfExists('verification_challenges'); }
};

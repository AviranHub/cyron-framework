<?php
namespace App\Database\Migrations;
use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration {
    public static function up() {
        Schema::create('two_factor_recovery_codes', function (TableBuilder $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('code_hash', 64);
            $table->timestamp('used_at')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public static function down() { Schema::dropIfExists('two_factor_recovery_codes'); }
};

<?php
namespace App\Database\Migrations;
use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration {
    public static function up() {
        Schema::table('users', function (TableBuilder $table) {
            $table->string('status', 20)->default('active');
            $table->string('remember_token', 191)->nullable();
            $table->integer('login_count')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('suspended_until')->nullable();
            $table->integer('primary_role_id')->nullable();
            $table->index('status');
        });
    }
    public static function down() {
        Schema::table('users', function (TableBuilder $table) {
            $table->dropColumn('status');
            $table->dropColumn('remember_token');
            $table->dropColumn('login_count');
            $table->dropColumn('last_login_at');
            $table->dropColumn('suspended_until');
            $table->dropColumn('primary_role_id');
        });
    }
};
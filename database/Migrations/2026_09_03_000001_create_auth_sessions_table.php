<?php
namespace App\Database\Migrations;
use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;

return new class extends Migration {
 public static function up() {
  Schema::create('auth_sessions', function (TableBuilder $table) {
   $table->id();
   $table->bigInteger('user_id')->unsigned();
   $table->string('token_hash', 64)->unique();
   $table->string('ip_address',45)->nullable();
   $table->text('user_agent')->nullable();
   $table->timestamp('last_seen_at')->nullable();
   $table->timestamp('revoked_at')->nullable();
   $table->timestamp('created_at')->useCurrent();
   $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
   $table->index('user_id');
   $table->index('revoked_at');
  });
 }
 public static function down(){ Schema::dropIfExists('auth_sessions'); }
};
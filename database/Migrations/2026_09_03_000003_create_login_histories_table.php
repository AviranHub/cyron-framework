<?php
namespace App\Database\Migrations;
use App\Database\Schema; use App\Database\TableBuilder; use App\Database\Migration;
return new class extends Migration {
 public static function up(){ Schema::create('login_histories', function(TableBuilder $table){
  $table->id(); $table->bigInteger('user_id')->unsigned()->nullable(); $table->boolean('successful')->default(0);
  $table->string('ip_address',45)->nullable(); $table->text('user_agent')->nullable(); $table->text('context')->nullable();
  $table->timestamp('occurred_at')->useCurrent(); $table->index('user_id'); $table->index('occurred_at');
  $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
 });}
 public static function down(){Schema::dropIfExists('login_histories');}
};
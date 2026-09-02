<?php
use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;
return new class extends Migration {
 public static function up(){Schema::create('login_histories',function(TableBuilder $table){
  $table->id();$table->integer('user_id')->nullable()->index();$table->integer('successful')->index();
  $table->string('ip_address',64)->nullable()->index();$table->text('user_agent')->nullable();
  $table->json('context')->nullable();$table->timestamp('occurred_at')->index();
 });}
 public static function down(){Schema::dropIfExists('login_histories');}
};
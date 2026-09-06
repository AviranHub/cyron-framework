<?php
namespace App\Database\Migrations;
use App\Database\Schema; use App\Database\TableBuilder; use App\Database\Migration;
return new class extends Migration {
 public static function up(){ Schema::create('login_attempts', function(TableBuilder $table){
  $table->id(); $table->string('key',191); $table->boolean('successful')->default(0);
  $table->timestamp('occurred_at')->useCurrent(); $table->index('key'); $table->index('occurred_at');
 });}
 public static function down(){Schema::dropIfExists('login_attempts');}
};
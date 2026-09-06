<?php
namespace App\Database\Migrations;
use App\Database\Schema; use App\Database\TableBuilder; use App\Database\Migration;
return new class extends Migration {
 public static function up(){ Schema::create('audit_logs', function(TableBuilder $table){
  $table->id(); $table->bigInteger('actor_id')->unsigned()->nullable(); $table->string('action',191);
  $table->text('context')->nullable(); $table->timestamp('occurred_at')->useCurrent();
  $table->index('actor_id'); $table->index('action'); $table->index('occurred_at');
  $table->foreign('actor_id')->references('id')->on('users')->onDelete('SET NULL');
 });}
 public static function down(){Schema::dropIfExists('audit_logs');}
};
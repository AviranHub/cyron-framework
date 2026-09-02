<?php
namespace App\Database\Migrations;
use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;
return new class extends Migration {
 public static function up(){ Schema::table('user_activities', function(TableBuilder $table){ $table->string('event',191)->nullable(); $table->string('label',191)->nullable(); $table->json('properties')->nullable(); $table->index('event'); }); }
 public static function down(){ Schema::table('user_activities', function(TableBuilder $table){ $table->dropColumn('event');$table->dropColumn('label');$table->dropColumn('properties'); }); }
};
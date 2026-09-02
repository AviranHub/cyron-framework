<?php
use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;
return new class extends Migration {
 public static function up(){Schema::create('audit_logs',function(TableBuilder $table){$table->id();$table->integer('actor_id')->nullable()->index();$table->string('action',191)->index();$table->json('context')->nullable();$table->timestamp('occurred_at')->index();});}
 public static function down(){Schema::dropIfExists('audit_logs');}
};
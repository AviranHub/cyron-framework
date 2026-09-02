<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('subscriptions', function (TableBuilder $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 20, 2);
            $table->integer('duration')->default(7); // مدت اعتبار به روز
            $table->boolean('allows_download'); // امکان دانلود کتاب‌ها
            $table->integer('counts_download')->default(5); // امکان دانلود کتاب‌ها
            $table->tinyInteger('offer', true)->default(0);
			$table->json('others')->nullable();
            $table->timestamps();
        });
    }

    public static function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
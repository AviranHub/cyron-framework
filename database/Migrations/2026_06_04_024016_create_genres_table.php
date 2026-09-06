<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('genres', function (TableBuilder $table) {
            // کلید اصلی
            $table->id();
            // // رشته‌ها
            $table->string('name');
            $table->string('description');
            // تایم‌استمپ‌ها (با softDeletes)
            $table->timestamps(true); // created_at, updated_at, deleted_at
            // اگر softDeletes جدا می‌خواهید:
            $table->softDeletes();
        });
    }

    public static function down()
    {
        Schema::dropIfExists('genres');
    }
};
<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('book_categories', function (TableBuilder $table) {
            // کلید اصلی
            $table->id();
            $table->string('name');
            $table->string('slug');
            // تایم‌استمپ‌ها (با softDeletes)
            $table->timestamps(true); // created_at, updated_at, deleted_at
        });
    }

    public static function down()
    {
        Schema::dropIfExists('book_categorys');
    }
};

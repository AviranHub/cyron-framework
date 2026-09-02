<?php

namespace App\Database\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;
use App\Database\Migration;


return new class extends Migration
{
    public static function up()
    {
        Schema::create('tag_lists', function (TableBuilder $table) {
            $table->id();

            // رابطه با tag
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            // رابطه Polymorphic (هم با مقالات هم با کتاب‌ها)
            $table->string('taggable_type'); // App\Models\Article یا App\Models\Book
            $table->unsignedBigInteger('taggable_id');

            // تاریخ ایجاد
            $table->timestamp('tagged_at')->useCurrent();

            // ایندکس‌ها
            $table->index(['taggable_type', 'taggable_id']);
            $table->index(['tag_id', 'taggable_type']);
            $table->unique(['tag_id', 'taggable_type', 'taggable_id']); // جلوگیری از تکراری
        });
    }

    public static function down()
    {
        Schema::dropIfExists('tag_lists');
    }
};
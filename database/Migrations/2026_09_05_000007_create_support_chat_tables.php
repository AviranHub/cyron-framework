<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use App\Database\Schema;
use App\Database\TableBuilder;

return new class extends Migration
{
    public static function up()
    {
        Schema::create('chat_conversations', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('status', ['open', 'closed'], 'open');
            $table->string('subject', 191, true);
            $table->timestamp('last_message_at', true)->index();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('chat_messages', function (TableBuilder $table) {
            $table->id();
            $table->bigInteger('conversation_id')->unsigned();
            $table->bigInteger('sender_id')->unsigned();
            $table->enum('sender_type', ['user', 'admin']);
            $table->text('body');
            $table->timestamp('read_at', true);
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_type', 'read_at']);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
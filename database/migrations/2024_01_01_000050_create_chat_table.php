<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->nullable();
            $table->string('type', 50)->default('order_chat');
            // order_chat | support_chat | admin_chat
            $table->string('status', 30)->default('active'); // active | closed | archived
            $table->timestamps();

            $table->index('order_id');
            $table->index('type');
        });

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->primary(['conversation_id', 'user_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('chat_conversations')->onDelete('cascade');
            $table->uuid('sender_id');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->text('content');
            $table->string('message_type', 20)->default('text');
            // text | image | location | audio | file
            $table->string('attachment_url')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['conversation_id', 'created_at']);
            $table->index('sender_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_participants');
        Schema::dropIfExists('chat_conversations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('live_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_key', 64)->unique();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['waiting', 'active', 'closed'])->default('waiting');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('live_chat_sessions')->cascadeOnDelete();
            $table->enum('sender_type', ['visitor', 'agent']);
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
        Schema::dropIfExists('live_chat_sessions');
    }
};

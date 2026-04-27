<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('voice_note_path')->nullable();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('assigned_type', ['admin', 'freelancer'])->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->date('due_date')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['assigned_type', 'assigned_to']);
            $table->index(['priority', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_tasks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancer_invoices', function (Blueprint $table) {
            // Remove file_path column if it exists
            if (Schema::hasColumn('freelancer_invoices', 'file_path')) {
                $table->dropColumn('file_path');
            }

            // Add new columns for form-based invoices
            $table->string('invoice_number')->unique()->after('project_id');
            $table->decimal('amount', 12, 2)->after('invoice_number');
            $table->text('description')->nullable()->after('amount');
            $table->date('due_date')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('freelancer_invoices', function (Blueprint $table) {
            // Restore file_path and remove new columns
            $table->string('file_path')->nullable();
            $table->dropColumn(['invoice_number', 'amount', 'description', 'due_date']);
        });
    }
};

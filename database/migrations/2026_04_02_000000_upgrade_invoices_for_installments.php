<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('amount', 'total_amount');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
        });

        // Existing fully-paid invoices: set paid_amount = total_amount
        DB::statement("UPDATE invoices SET paid_amount = total_amount WHERE status = 'paid'");
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'amount');
        });
    }
};

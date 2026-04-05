<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency', 3)->default('TZS')->after('amount');
            $table->decimal('exchange_rate', 14, 4)->nullable()->after('currency');
            $table->decimal('converted_amount', 14, 2)->nullable()->after('exchange_rate');
            $table->timestamp('expires_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'converted_amount', 'expires_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('payment_card_enabled')->default(false)->after('background_color');
            $table->boolean('payment_paypal_enabled')->default(false)->after('payment_card_enabled');
            $table->boolean('payment_selcom_enabled')->default(false)->after('payment_paypal_enabled');
            $table->boolean('payment_mpesa_enabled')->default(false)->after('payment_selcom_enabled');
            $table->boolean('payment_bank_enabled')->default(false)->after('payment_mpesa_enabled');
            $table->string('mpesa_paybill', 120)->nullable()->after('payment_bank_enabled');
            $table->text('payment_notes')->nullable()->after('mpesa_paybill');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_card_enabled',
                'payment_paypal_enabled',
                'payment_selcom_enabled',
                'payment_mpesa_enabled',
                'payment_bank_enabled',
                'mpesa_paybill',
                'payment_notes',
            ]);
        });
    }
};

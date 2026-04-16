<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('item_type', 20)->default('project')->after('status');
            $table->boolean('is_purchasable')->default(false)->after('item_type');
            $table->decimal('price', 12, 2)->nullable()->after('is_purchasable');
            $table->string('currency', 10)->default('USD')->after('price');
            $table->string('purchase_url')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn([
                'item_type',
                'is_purchasable',
                'price',
                'currency',
                'purchase_url',
            ]);
        });
    }
};

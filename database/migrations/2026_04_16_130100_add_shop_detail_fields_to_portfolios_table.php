<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->longText('product_description')->nullable()->after('description');
            $table->json('product_gallery')->nullable()->after('image_path');
            $table->text('extra_info')->nullable()->after('results');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['product_description', 'product_gallery', 'extra_info']);
        });
    }
};

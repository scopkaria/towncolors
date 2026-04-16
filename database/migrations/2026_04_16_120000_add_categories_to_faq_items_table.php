<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('faq_items', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('category');
        });

        DB::table('faq_items')->orderBy('id')->chunkById(200, function ($items): void {
            foreach ($items as $item) {
                DB::table('faq_items')
                    ->where('id', $item->id)
                    ->update([
                        'categories' => json_encode([$item->category ?: 'General']),
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('faq_items', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};

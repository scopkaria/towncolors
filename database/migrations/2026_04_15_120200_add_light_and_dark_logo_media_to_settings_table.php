<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('light_logo_media_id')->nullable()->after('logo_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('dark_logo_media_id')->nullable()->after('light_logo_media_id')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('light_logo_media_id');
            $table->dropConstrainedForeignId('dark_logo_media_id');
        });
    }
};
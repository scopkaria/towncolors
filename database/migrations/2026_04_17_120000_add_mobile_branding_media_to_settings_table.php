<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('mobile_logo_media_id')->nullable()->after('dark_logo_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('mobile_icon_media_id')->nullable()->after('mobile_logo_media_id')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mobile_icon_media_id');
            $table->dropConstrainedForeignId('mobile_logo_media_id');
        });
    }
};

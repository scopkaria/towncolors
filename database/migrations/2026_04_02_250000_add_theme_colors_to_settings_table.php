<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('primary_color')->default('#F97316')->after('bank_details');
            $table->string('secondary_color')->default('#EA580C')->after('primary_color');
            $table->string('background_color')->default('#F5F5F4')->after('secondary_color');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['primary_color', 'secondary_color', 'background_color']);
        });
    }
};

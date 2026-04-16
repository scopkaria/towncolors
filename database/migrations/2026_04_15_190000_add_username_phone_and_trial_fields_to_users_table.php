<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->nullable()->after('name');
            $table->string('phone', 40)->nullable()->after('email');
            $table->date('trial_start_date')->nullable()->after('profile_image_path');
            $table->date('trial_end_date')->nullable()->after('trial_start_date');
            $table->timestamp('trial_used_at')->nullable()->after('trial_end_date');

            $table->unique('username');
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['phone']);

            $table->dropColumn([
                'username',
                'phone',
                'trial_start_date',
                'trial_end_date',
                'trial_used_at',
            ]);
        });
    }
};

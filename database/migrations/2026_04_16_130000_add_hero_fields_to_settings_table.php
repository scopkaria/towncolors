<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('service_hero_media_id')->nullable()->after('payment_notes')->constrained('media')->nullOnDelete();
            $table->foreignId('blog_hero_media_id')->nullable()->after('service_hero_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('shop_hero_media_id')->nullable()->after('blog_hero_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('cloud_hero_media_id')->nullable()->after('shop_hero_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('portfolio_hero_media_id')->nullable()->after('cloud_hero_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('about_hero_media_id')->nullable()->after('portfolio_hero_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('contact_hero_media_id')->nullable()->after('about_hero_media_id')->constrained('media')->nullOnDelete();

            $table->string('service_hero_subtitle', 255)->nullable()->after('contact_hero_media_id');
            $table->string('blog_hero_subtitle', 255)->nullable()->after('service_hero_subtitle');
            $table->string('shop_hero_subtitle', 255)->nullable()->after('blog_hero_subtitle');
            $table->string('cloud_hero_subtitle', 255)->nullable()->after('shop_hero_subtitle');
            $table->string('portfolio_hero_subtitle', 255)->nullable()->after('cloud_hero_subtitle');
            $table->string('about_hero_subtitle', 255)->nullable()->after('portfolio_hero_subtitle');
            $table->string('contact_hero_subtitle', 255)->nullable()->after('about_hero_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_hero_media_id');
            $table->dropConstrainedForeignId('blog_hero_media_id');
            $table->dropConstrainedForeignId('shop_hero_media_id');
            $table->dropConstrainedForeignId('cloud_hero_media_id');
            $table->dropConstrainedForeignId('portfolio_hero_media_id');
            $table->dropConstrainedForeignId('about_hero_media_id');
            $table->dropConstrainedForeignId('contact_hero_media_id');

            $table->dropColumn([
                'service_hero_subtitle',
                'blog_hero_subtitle',
                'shop_hero_subtitle',
                'cloud_hero_subtitle',
                'portfolio_hero_subtitle',
                'about_hero_subtitle',
                'contact_hero_subtitle',
            ]);
        });
    }
};

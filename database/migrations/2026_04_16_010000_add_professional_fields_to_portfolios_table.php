<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('client_name')->nullable()->after('description');
            $table->string('project_url')->nullable()->after('client_name');
            $table->string('industry')->nullable()->after('project_url');
            $table->string('country', 120)->nullable()->after('industry');
            $table->unsignedSmallInteger('completion_year')->nullable()->after('country');
            $table->string('duration')->nullable()->after('completion_year');
            $table->json('services')->nullable()->after('duration');
            $table->json('technologies')->nullable()->after('services');
            $table->text('results')->nullable()->after('technologies');
            $table->boolean('featured')->default(false)->after('results');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'client_name',
                'project_url',
                'industry',
                'country',
                'completion_year',
                'duration',
                'services',
                'technologies',
                'results',
                'featured',
            ]);
        });
    }
};

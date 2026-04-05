<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the pivot table
        Schema::create('category_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('project_categories')->cascadeOnDelete();
            $table->unique(['project_id', 'category_id']);
        });

        // 2. Migrate existing single category_id → pivot rows
        DB::statement('
            INSERT INTO category_project (project_id, category_id)
            SELECT id, category_id
            FROM projects
            WHERE category_id IS NOT NULL
        ');

        // 3. Drop the old category_id FK and column from projects
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        // Restore category_id column (restores first category from pivot for each project)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('status')
                ->constrained('project_categories')
                ->nullOnDelete();
        });

        DB::statement('
            UPDATE projects p
            JOIN (
                SELECT project_id, MIN(category_id) AS category_id
                FROM category_project
                GROUP BY project_id
            ) cp ON p.id = cp.project_id
            SET p.category_id = cp.category_id
        ');

        Schema::dropIfExists('category_project');
    }
};

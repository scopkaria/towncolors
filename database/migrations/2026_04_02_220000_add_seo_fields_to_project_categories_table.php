<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('featured_image')->nullable()->after('image_path');
            $table->text('long_description')->nullable()->after('description');
        });

        // Back-fill slugs for existing rows
        DB::table('project_categories')->orderBy('id')->each(function ($row) {
            $base = Str::slug($row->name);
            $slug = $base;
            $i = 1;
            while (DB::table('project_categories')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('project_categories')->where('id', $row->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('project_categories', function (Blueprint $table) {
            $table->dropColumn(['slug', 'featured_image', 'long_description']);
        });
    }
};

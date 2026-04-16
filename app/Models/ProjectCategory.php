<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'long_description',
        'image_path', 'featured_image', 'gallery_images', 'color', 'parent_id',
        'price_range', 'estimated_duration',
    ];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'category_project', 'category_id', 'project_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProjectCategory::class, 'parent_id')->orderBy('name');
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }
}

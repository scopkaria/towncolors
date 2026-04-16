<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'product_description',
        'client_name',
        'project_url',
        'industry',
        'country',
        'completion_year',
        'duration',
        'services',
        'technologies',
        'results',
        'extra_info',
        'featured',
        'image_path',
        'product_gallery',
        'status',
        'item_type',
        'is_purchasable',
        'price',
        'currency',
        'purchase_url',
    ];

    protected $casts = [
        'completion_year' => 'integer',
        'services' => 'array',
        'technologies' => 'array',
        'product_gallery' => 'array',
        'featured' => 'boolean',
        'is_purchasable' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Unknown freelancer',
        ]);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}

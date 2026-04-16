<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'price_monthly',
        'price_yearly',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features'      => 'array',
        'is_active'     => 'boolean',
        'price_monthly' => 'float',
        'price_yearly'  => 'float',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /** Tailwind ring / text colour per plan colour slug */
    public function colorClasses(): string
    {
        return match ($this->color) {
            'blue'   => 'ring-blue-400 text-blue-700 bg-blue-50',
            'purple' => 'ring-purple-400 text-purple-700 bg-purple-50',
            'black'  => 'ring-slate-900 text-white bg-slate-950',
            default  => 'ring-emerald-400 text-emerald-700 bg-emerald-50',
        };
    }

    /** Badge colour helpers */
    public function badgeClass(): string
    {
        return match ($this->color) {
            'blue'   => 'bg-blue-100 text-blue-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'black'  => 'bg-slate-900 text-white',
            default  => 'bg-emerald-100 text-emerald-700',
        };
    }
}

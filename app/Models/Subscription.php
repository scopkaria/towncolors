<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'billing_cycle',
        'start_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }

    public function daysUntilExpiry(): int
    {
        return max(0, now()->startOfDay()->diffInDays($this->expiry_date, false));
    }

    public function isExpiringSoon(int $days = 5): bool
    {
        return $this->isActive() && $this->daysUntilExpiry() <= $days;
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'active'    => 'bg-emerald-100 text-emerald-700',
            'expired'   => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-stone-100 text-stone-600',
            default     => 'bg-amber-100 text-amber-700',
        };
    }
}

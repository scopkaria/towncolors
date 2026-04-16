<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRequest extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'billing_cycle', 'payment_method', 'payment_reference', 'notes',
        'status', 'reviewed_by', 'reviewed_at', 'admin_notes',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function plan(): BelongsTo   { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
            default    => 'bg-amber-100 text-amber-700',
        };
    }
}

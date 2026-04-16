<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientChecklistItem extends Model
{
    protected $fillable = [
        'client_id',
        'created_by',
        'title',
        'status',
        'sort_order',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'completed' => 'bg-emerald-100 text-emerald-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            default => 'bg-stone-100 text-stone-600',
        };
    }
}
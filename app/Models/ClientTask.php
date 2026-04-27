<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTask extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'description',
        'voice_note_path',
        'image_path',
        'status',
        'priority',
        'assigned_type',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'due_date',
        'admin_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'completed' => 'bg-emerald-100 text-emerald-700',
            'in_progress' => 'bg-blue-100 text-blue-700',
            'assigned' => 'bg-amber-100 text-amber-700',
            default => 'bg-stone-100 text-stone-700',
        };
    }

    public function priorityBadge(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-red-100 text-red-700',
            'high' => 'bg-orange-100 text-orange-700',
            'medium' => 'bg-amber-100 text-amber-700',
            default => 'bg-stone-100 text-stone-700',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_key',
        'visitor_name',
        'visitor_email',
        'agent_id',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, 'session_id');
    }
}

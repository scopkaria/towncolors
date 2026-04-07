<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'sender_type',
        'agent_id',
        'body',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(LiveChatSession::class, 'session_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}

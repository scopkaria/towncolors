<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = ['type', 'project_id'];

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Find the conversation for a project, or create one and add participants.
     */
    public static function findOrCreateForProject(Project $project): static
    {
        /** @var static|null $conversation */
        $conversation = static::where('type', 'project')
            ->where('project_id', $project->id)
            ->first();

        if (! $conversation) {
            $conversation = static::create([
                'type'       => 'project',
                'project_id' => $project->id,
            ]);
        }

        // Ensure all stakeholders are participants
        $existing = $conversation->participants()->pluck('user_id');

        $needed = collect();
        if ($project->client_id) {
            $needed->push($project->client_id);
        }
        if ($project->freelancer_id) {
            $needed->push($project->freelancer_id);
        }
        User::where('role', 'admin')->pluck('id')->each(fn ($id) => $needed->push($id));

        $needed->unique()->diff($existing)->each(function ($userId) use ($conversation) {
            $conversation->participants()->create(['user_id' => $userId]);
        });

        return $conversation;
    }
}

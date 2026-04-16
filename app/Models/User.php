<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Subscription;
use App\Models\ClientFile;
use App\Models\ClientFolder;
use App\Models\ClientChecklistItem;
use App\Models\SubscriptionRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'password',
        'must_change_password',
        'onboarding_completed',
        'profile_image_path',
        'trial_start_date',
        'trial_end_date',
        'trial_used_at',
        'push_token',
        'push_platform',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
        'must_change_password' => 'boolean',
        'onboarding_completed' => 'boolean',
        'trial_start_date' => 'date',
        'trial_end_date' => 'date',
        'trial_used_at' => 'datetime',
    ];

    public function dashboardPath(): string
    {
        return ($this->role ?? UserRole::CLIENT)->dashboardPath();
    }

    public function freelancerInvoices(): HasMany
    {
        return $this->hasMany(FreelancerInvoice::class, 'freelancer_id');
    }

    public function conversations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function hasUsedTrial(): bool
    {
        return $this->trial_used_at !== null;
    }

    public function hasActiveTrial(): bool
    {
        if (! $this->trial_start_date || ! $this->trial_end_date) {
            return false;
        }

        return now()->toDateString() <= $this->trial_end_date->toDateString();
    }

    public function canStartTrial(): bool
    {
        return ! $this->hasUsedTrial();
    }

    public function hasFullAccess(): bool
    {
        if ($this->role !== UserRole::CLIENT) {
            return true;
        }

        return $this->hasActiveSubscription() || $this->hasActiveTrial();
    }

    public function startFreeTrial(int $days = 5): void
    {
        $start = now()->startOfDay();
        $end = $start->copy()->addDays(max(1, $days))->subDay();

        $this->forceFill([
            'trial_start_date' => $start->toDateString(),
            'trial_end_date' => $end->toDateString(),
            'trial_used_at' => $this->trial_used_at ?? now(),
        ])->save();
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>=', now()->toDateString())
            ->with('plan')
            ->latest()
            ->first();
    }

    public function clientFiles(): HasMany
    {
        return $this->hasMany(ClientFile::class, 'user_id');
    }

    public function clientFolders(): HasMany
    {
        return $this->hasMany(ClientFolder::class, 'user_id');
    }

    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ClientChecklistItem::class, 'client_id')->orderBy('sort_order')->orderBy('id');
    }

    public function createdChecklistItems(): HasMany
    {
        return $this->hasMany(ClientChecklistItem::class, 'created_by');
    }

    public function profileImageUrl(): ?string
    {
        return $this->profile_image_path ? asset('storage/' . $this->profile_image_path) : null;
    }

    public function avatarInitials(): string
    {
        return collect(explode(' ', $this->name ?? 'TC'))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    }
}

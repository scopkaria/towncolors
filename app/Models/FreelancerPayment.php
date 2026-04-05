<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FreelancerPayment extends Model
{
    protected $fillable = [
        'project_id',
        'freelancer_id',
        'agreed_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'agreed_amount' => 'decimal:2',
        'paid_amount'   => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FreelancerPaymentLog::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->agreed_amount - (float) $this->paid_amount;
    }

    public function formattedAgreed(): string
    {
        return 'TZS ' . number_format((float) $this->agreed_amount, 2);
    }

    public function formattedPaid(): string
    {
        return 'TZS ' . number_format((float) $this->paid_amount, 2);
    }

    public function formattedRemaining(): string
    {
        return 'TZS ' . number_format($this->remaining_amount, 2);
    }
}

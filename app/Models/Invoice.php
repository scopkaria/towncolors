<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    public const FALLBACK_RATE = 2500.0000;

    protected $fillable = [
        'project_id',
        'total_amount',
        'paid_amount',
        'currency',
        'exchange_rate',
        'converted_amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'converted_amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculated remaining amount (not stored).
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }

    /**
     * Get the total invoice amount in a specific currency.
     */
    public function amountIn(string $currency): float
    {
        if ($this->currency === $currency) {
            return (float) $this->total_amount;
        }

        $rate = (float) ($this->exchange_rate ?: self::FALLBACK_RATE);

        if ($this->currency === 'USD' && $currency === 'TZS') {
            return (float) $this->total_amount * $rate;
        }

        if ($this->currency === 'TZS' && $currency === 'USD') {
            return $rate > 0 ? (float) $this->total_amount / $rate : 0;
        }

        return (float) $this->total_amount;
    }

    /**
     * Get the paid amount in a specific currency.
     */
    public function paidAmountIn(string $currency): float
    {
        if ($this->currency === $currency) {
            return (float) $this->paid_amount;
        }

        $rate = (float) ($this->exchange_rate ?: self::FALLBACK_RATE);

        if ($this->currency === 'USD' && $currency === 'TZS') {
            return (float) $this->paid_amount * $rate;
        }

        if ($this->currency === 'TZS' && $currency === 'USD') {
            return $rate > 0 ? (float) $this->paid_amount / $rate : 0;
        }

        return (float) $this->paid_amount;
    }

    public function formattedAmount(?string $currency = null): string
    {
        $currency = $currency ?? $this->currency;
        $amount = $this->amountIn($currency);
        $symbol = $currency === 'USD' ? '$' : 'TZS ';

        return $symbol . number_format($amount, 2);
    }

    public function formattedPaidAmount(): string
    {
        $symbol = $this->currency === 'USD' ? '$' : 'TZS ';

        return $symbol . number_format((float) $this->paid_amount, 2);
    }

    public function formattedRemainingAmount(): string
    {
        $symbol = $this->currency === 'USD' ? '$' : 'TZS ';

        return $symbol . number_format($this->remaining_amount, 2);
    }
}

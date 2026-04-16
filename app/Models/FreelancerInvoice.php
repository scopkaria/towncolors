<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerInvoice extends Model
{
    protected $fillable = [
        'freelancer_id',
        'project_id',
        'invoice_number',
        'amount',
        'description',
        'due_date',
        'status',
        'rejection_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id')->withDefault([
            'name' => 'Unknown freelancer',
        ]);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withDefault([
            'title' => 'Unknown project',
        ]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => 'Pending',
        };
    }

    public function statusClasses(): string
    {
        return match ($this->status) {
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-600',
            default    => 'border-amber-200 bg-amber-50 text-amber-700',
        };
    }

    /**
     * Generate a unique invoice number.
     * Format: INV-YYYY-XXXXX (e.g., INV-2026-00001)
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('INV-%d-%05d', $year, $count);
    }
}

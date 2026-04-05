<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerPaymentLog extends Model
{
    protected $fillable = [
        'freelancer_payment_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function freelancerPayment(): BelongsTo
    {
        return $this->belongsTo(FreelancerPayment::class);
    }
}

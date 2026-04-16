<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwarePurchaseRequest extends Model
{
    protected $fillable = [
        'portfolio_id',
        'user_id',
        'name',
        'email',
        'phone',
        'company',
        'payment_method',
        'payment_reference',
        'message',
        'status',
        'admin_note',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

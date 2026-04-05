<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'project_type',
        'message',
        'status',
        'admin_notes',
        'converted_user_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function convertedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_user_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Human-readable status label with colour class. */
    public function statusBadge(): array
    {
        return match ($this->status) {
            'contacted'  => ['label' => 'Contacted',  'class' => 'border-sky-200   bg-sky-50   text-sky-700'],
            'converted'  => ['label' => 'Converted',  'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
            default      => ['label' => 'New',        'class' => 'border-orange-200 bg-orange-50 text-brand-primary'],
        };
    }

    /** The project type options shared with forms and display. */
    public static function projectTypes(): array
    {
        return [
            'web_development'   => 'Web Development',
            'mobile_app'        => 'Mobile App',
            'ui_ux_design'      => 'UI / UX Design',
            'ecommerce'         => 'E-commerce',
            'branding'          => 'Branding & Identity',
            'seo_marketing'     => 'SEO & Digital Marketing',
            'other'             => 'Other',
        ];
    }
}

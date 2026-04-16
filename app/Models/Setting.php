<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'logo_path',
        'logo_media_id',
        'light_logo_media_id',
        'dark_logo_media_id',
        'phone',
        'email',
        'address',
        'bank_details',
        'primary_color',
        'secondary_color',
        'background_color',
        'payment_card_enabled',
        'payment_paypal_enabled',
        'payment_selcom_enabled',
        'payment_mpesa_enabled',
        'payment_bank_enabled',
        'mpesa_paybill',
        'payment_notes',
        'service_hero_media_id',
        'blog_hero_media_id',
        'shop_hero_media_id',
        'cloud_hero_media_id',
        'portfolio_hero_media_id',
        'about_hero_media_id',
        'contact_hero_media_id',
        'service_hero_subtitle',
        'blog_hero_subtitle',
        'shop_hero_subtitle',
        'cloud_hero_subtitle',
        'portfolio_hero_subtitle',
        'about_hero_subtitle',
        'contact_hero_subtitle',
    ];

    protected $casts = [
        'payment_card_enabled' => 'boolean',
        'payment_paypal_enabled' => 'boolean',
        'payment_selcom_enabled' => 'boolean',
        'payment_mpesa_enabled' => 'boolean',
        'payment_bank_enabled' => 'boolean',
    ];

    public static function instance(): static
    {
        return static::firstOrCreate([]);
    }

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function lightLogoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'light_logo_media_id');
    }

    public function darkLogoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'dark_logo_media_id');
    }

    /**
     * Returns the logo URL, preferring the Media Library selection
     * over the legacy direct-upload logo_path.
     */
    public function logoUrl(): ?string
    {
        if ($this->logo_media_id && $this->logoMedia) {
            return $this->logoMedia->url();
        }

        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function themedLogoUrl(bool $darkMode = false): ?string
    {
        if ($darkMode && $this->light_logo_media_id && $this->lightLogoMedia) {
            return $this->lightLogoMedia->url();
        }

        if (! $darkMode && $this->dark_logo_media_id && $this->darkLogoMedia) {
            return $this->darkLogoMedia->url();
        }

        return $this->logoUrl();
    }

    /**
     * Returns the absolute filesystem path for the logo (used by DomPDF).
     */
    public function logoAbsolutePath(): ?string
    {
        if ($this->logo_media_id && $this->logoMedia) {
            return storage_path('app/public/' . $this->logoMedia->file_path);
        }

        return $this->logo_path ? storage_path('app/public/' . $this->logo_path) : null;
    }

    /**
     * Checks whether a logo file actually exists on disk (for PDF guards).
     */
    public function logoFileExists(): bool
    {
        $path = $this->logoAbsolutePath();

        return $path !== null && file_exists($path);
    }

    public function enabledPaymentMethods(): array
    {
        $methods = [];

        if ($this->payment_card_enabled) {
            $methods['card'] = 'Card payments';
        }
        if ($this->payment_paypal_enabled) {
            $methods['paypal'] = 'PayPal';
        }
        if ($this->payment_selcom_enabled) {
            $methods['selcom'] = 'Selcom';
        }
        if ($this->payment_mpesa_enabled) {
            $methods['mpesa'] = 'M-Pesa / Paybill';
        }
        if ($this->payment_bank_enabled) {
            $methods['bank'] = 'Bank transfer';
        }

        return $methods;
    }

    public function heroMediaUrl(string $page): ?string
    {
        $field = $page . '_hero_media_id';
        $mediaId = (int) ($this->{$field} ?? 0);
        if ($mediaId <= 0) {
            return null;
        }

        $media = Media::find($mediaId);

        return $media?->url();
    }

    public function heroSubtitle(string $page): ?string
    {
        $field = $page . '_hero_subtitle';

        return $this->{$field} ?: null;
    }
}

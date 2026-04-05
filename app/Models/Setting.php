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
        'phone',
        'email',
        'address',
        'bank_details',
        'primary_color',
        'secondary_color',
        'background_color',
    ];

    public static function instance(): static
    {
        return static::firstOrCreate([]);
    }

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
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
}

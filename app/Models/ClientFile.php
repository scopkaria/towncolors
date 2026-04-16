<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClientFile extends Model
{
    protected $fillable = [
        'user_id',
        'folder_id',
        'uploaded_by',
        'original_name',
        'path',
        'mime_type',
        'size',
        'description',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(ClientFolder::class, 'folder_id');
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024)        return "{$bytes} B";
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824)  return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'video/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isArchive(): bool
    {
        return in_array($this->mime_type, ['application/zip', 'application/x-zip-compressed', 'application/x-rar-compressed']);
    }

    public function isPreviewable(): bool
    {
        return $this->isImage() || $this->isPdf() || $this->isVideo();
    }

    public function iconName(): string
    {
        if ($this->isImage())   return 'image';
        if ($this->isVideo())   return 'video';
        if ($this->isPdf())     return 'pdf';
        if ($this->isArchive()) return 'archive';
        return 'document';
    }

    public function url(): string
    {
        return Storage::disk('client_files')->url($this->path);
    }
}

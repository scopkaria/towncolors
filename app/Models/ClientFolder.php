<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientFolder extends Model
{
    protected $fillable = ['user_id', 'parent_id', 'name'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ClientFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ClientFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ClientFile::class, 'folder_id');
    }
}

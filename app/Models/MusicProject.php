<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MusicProject extends Model
{
    protected $fillable = ['title', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(MusicTrack::class)->orderBy('sort_order');
    }

    public function proAccounts(): BelongsToMany
    {
        return $this->belongsToMany(ProAccount::class, 'pro_account_music_project')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

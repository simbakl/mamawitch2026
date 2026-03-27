<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Release extends Model
{
    protected $fillable = [
        'title', 'slug', 'type', 'cover', 'release_date', 'description',
        'player_embed_url', 'credits',
        'spotify_url', 'bandcamp_url', 'apple_music_url', 'deezer_url', 'soundcloud_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Release $release) {
            if (empty($release->slug)) {
                $release->slug = Str::slug($release->title);
            }
        });
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('track_number');
    }

    public function hasPlatformLinks(): bool
    {
        return $this->spotify_url || $this->bandcamp_url || $this->apple_music_url
            || $this->deezer_url || $this->soundcloud_url;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}

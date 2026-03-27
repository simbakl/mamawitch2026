<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'youtube_url', 'category', 'published_at', 'is_published'];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function getYoutubeIdAttribute(): ?string
    {
        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url ?? '', $matches);

        return $matches[1] ?? null;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}

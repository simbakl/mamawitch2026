<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'youtube_url',
        'news_category_id',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function hasDetailPage(): bool
    {
        return ! empty($this->body);
    }

    public function isScheduled(): bool
    {
        return $this->is_published && $this->published_at?->isFuture();
    }

    public function isVisible(): bool
    {
        return $this->is_published && ($this->published_at === null || $this->published_at->isPast());
    }

    public function scopeVisible($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeLatestPublished($query)
    {
        return $query->visible()->orderByDesc('published_at')->orderByDesc('created_at');
    }
}

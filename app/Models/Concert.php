<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Concert extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'date',
        'venue',
        'address',
        'postal_code',
        'city',
        'ticket_url',
        'type',
        'poster',
        'description',
        'status',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Concert $concert) {
            if (empty($concert->slug)) {
                $concert->slug = Str::slug($concert->title . '-' . $concert->date?->format('Y-m-d'));
            }
        });
    }

    public function isPast(): bool
    {
        return $this->date->isPast();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Annulé';
        }

        if ($this->status === 'soldout') {
            return 'Complet';
        }

        if ($this->isPast()) {
            return 'Passé';
        }

        return 'À venir';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now());
    }
}

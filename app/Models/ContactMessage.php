<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read'];

    protected static function booted(): void
    {
        static::created(fn () => Cache::forget('nav_badge_unread_messages'));
        static::updated(fn () => Cache::forget('nav_badge_unread_messages'));
    }

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}

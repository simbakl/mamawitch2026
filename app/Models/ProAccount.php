<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class ProAccount extends Model
{
    protected static function booted(): void
    {
        static::created(fn () => Cache::forget('nav_badge_pending_pro'));
        static::updated(fn () => Cache::forget('nav_badge_pending_pro'));
    }

    protected $fillable = [
        'user_id', 'pro_type_id', 'first_name', 'last_name', 'email',
        'structure', 'message', 'status', 'invitation_token',
        'invitation_sent_at', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'invitation_sent_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proType(): BelongsTo
    {
        return $this->belongsTo(ProType::class);
    }

    public function musicProjects(): BelongsToMany
    {
        return $this->belongsToMany(MusicProject::class, 'pro_account_music_project')->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'invited']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'photo',
        'instruments',
        'bio',
        'instagram',
        'facebook',
        'twitter',
        'youtube',
        'website',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (empty($member->slug)) {
                $member->slug = Str::slug($member->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasSocialLinks(): bool
    {
        return $this->instagram || $this->facebook || $this->twitter || $this->youtube || $this->website;
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class)->orderBy('sort_order');
    }

    public function techRequirement(): HasOne
    {
        return $this->hasOne(TechRequirement::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = ['member_id', 'name', 'category', 'notes', 'sort_order'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'instrument' => 'Instrument',
            'amp' => 'Ampli',
            'effect' => 'Effet / Pédale',
            'accessory' => 'Accessoire',
            default => $this->category,
        };
    }
}

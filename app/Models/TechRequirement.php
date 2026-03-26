<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechRequirement extends Model
{
    protected $fillable = ['member_id', 'monitors', 'microphones', 'power', 'monitoring', 'other'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

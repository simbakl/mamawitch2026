<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    protected $fillable = ['release_id', 'title', 'track_number', 'duration'];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
}

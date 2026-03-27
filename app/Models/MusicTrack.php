<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicTrack extends Model
{
    protected $fillable = ['music_project_id', 'title', 'file_path', 'file_name', 'duration', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(MusicProject::class, 'music_project_id');
    }
}

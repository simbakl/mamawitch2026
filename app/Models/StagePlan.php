<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StagePlan extends Model
{
    protected $fillable = ['name', 'image', 'elements', 'stage_width', 'stage_depth'];

    protected function casts(): array
    {
        return [
            'elements' => 'array',
        ];
    }
}

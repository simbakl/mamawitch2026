<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProContentPage extends Model
{
    protected $fillable = ['pro_content_type_id', 'body', 'data', 'files', 'updated_by'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'files' => 'array',
        ];
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ProContentType::class, 'pro_content_type_id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

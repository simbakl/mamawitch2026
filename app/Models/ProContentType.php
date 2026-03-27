<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProContentType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];

    public function proTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProType::class, 'pro_access_matrix')->withTimestamps();
    }

    public function contentPage(): HasOne
    {
        return $this->hasOne(ProContentPage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaticPage extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'is_published', 'show_in_menu', 'show_in_footer', 'menu_order'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_in_footer' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StaticPage $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeInMenu($query)
    {
        return $query->published()->where('show_in_menu', true)->orderBy('menu_order');
    }

    public function scopeInFooter($query)
    {
        return $query->published()->where('show_in_footer', true)->orderBy('menu_order');
    }
}

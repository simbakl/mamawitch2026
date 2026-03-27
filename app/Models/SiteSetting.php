<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * In-memory cache for the current request (avoids repeated Cache::get calls).
     */
    protected static array $memo = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$memo)) {
            return static::$memo[$key] ?? $default;
        }

        $value = Cache::rememberForever("site_setting_{$key}", function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        static::$memo[$key] = $value;

        return $value ?? $default;
    }

    /**
     * Load multiple settings at once (single DB query, populates cache).
     */
    public static function getMany(array $keys): array
    {
        $missing = array_filter($keys, fn ($k) => ! array_key_exists($k, static::$memo));

        if (! empty($missing)) {
            // Check which keys are not yet in the cache store
            $toFetch = [];
            foreach ($missing as $key) {
                $cached = Cache::get("site_setting_{$key}");
                if ($cached !== null) {
                    static::$memo[$key] = $cached;
                } else {
                    $toFetch[] = $key;
                }
            }

            // Single DB query for all uncached keys
            if (! empty($toFetch)) {
                $rows = static::whereIn('key', $toFetch)->pluck('value', 'key');
                foreach ($toFetch as $key) {
                    $value = $rows[$key] ?? null;
                    static::$memo[$key] = $value;
                    Cache::forever("site_setting_{$key}", $value);
                }
            }
        }

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = static::$memo[$key] ?? null;
        }

        return $result;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
        static::$memo[$key] = $value;
    }

    public static function clearCache(): void
    {
        static::pluck('key')->each(fn ($key) => Cache::forget("site_setting_{$key}"));
        static::$memo = [];
    }
}

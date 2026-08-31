<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    private static function cacheKey(string $key): string
    {
        return 'setting:'.$key;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever(
            self::cacheKey($key),
            fn () => static::query()->whereKey($key)->value('value') ?? $default
        );
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::cacheKey($key));
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function putBool(string $key, bool $value): void
    {
        self::put($key, $value ? '1' : '0');
    }
}

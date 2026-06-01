<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupLocation extends Model
{
    protected $fillable = [
        'key',
        'name',
        'sort_order',
    ];

    /**
     * Daftar lokasi untuk dropdown checkout: [key => ['key' => ..., 'name' => ...]].
     */
    public static function options(): array
    {
        return static::orderBy('sort_order')->orderBy('name')->get()
            ->mapWithKeys(fn ($loc) => [$loc->key => ['key' => $loc->key, 'name' => $loc->name]])
            ->all();
    }

    public static function findByKey(?string $key): ?self
    {
        return $key ? static::where('key', $key)->first() : null;
    }
}

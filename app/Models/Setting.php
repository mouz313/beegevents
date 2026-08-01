<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'label', 'group', 'type', 'description', 'is_public'])]
class Setting extends Model
{
    protected static ?array $cache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::$cache === null) {
            static::$cache = static::query()->pluck('value', 'key')->toArray();
        }

        return static::$cache[$key] ?? $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function forgetCache(): void
    {
        static::$cache = null;
    }
}

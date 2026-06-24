<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Read a setting value or return the provided default.
     */
    public static function value(string $key, ?string $default = null): ?string
    {
        return self::query()->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Persist a setting value.
     */
    public static function store(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}

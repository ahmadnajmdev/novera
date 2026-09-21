<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'values' => 'array',
            'is_pattern' => 'boolean',
        ];
    }

    public static function hashFor(string $source): string
    {
        return hash('sha256', trim($source));
    }

    protected static function booted(): void
    {
        static::saving(function (Translation $translation) {
            $translation->source_hash = static::hashFor($translation->source);
        });
    }
}

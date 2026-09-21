<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'settings' => 'array',
            'is_visible' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Blocks store translatable strings as ['en' => '...', 'ku' => '...'].
     * Reading falls back to the site default locale so a half-translated
     * block still renders instead of showing a blank section.
     */
    public function text(string $key, ?string $locale = null, ?string $fallback = null): ?string
    {
        $locale ??= app()->getLocale();
        $value = data_get($this->data, $key);

        if (is_array($value)) {
            return $value[$locale]
                ?? $value[config('app.fallback_locale')]
                ?? collect($value)->first(fn ($v) => filled($v))
                ?? $fallback;
        }

        return $value ?? $fallback;
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}

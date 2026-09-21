<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    public const CACHE_KEY = 'novera.settings';

    protected ?array $cache = null;

    protected function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        return $this->cache = Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::query()
                ->get()
                ->mapWithKeys(fn (Setting $setting) => [
                    $setting->key => [
                        'value' => $setting->value,
                        'translatable' => $setting->is_translatable,
                    ],
                ])
                ->all();
        });
    }

    /**
     * Translatable settings are stored as ['en' => '...', 'ku' => '...'] and
     * resolved against the active locale with a fallback chain, so a setting
     * that has not been translated yet still renders in the default language.
     */
    public function get(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        $entry = $this->all()[$key] ?? null;

        if ($entry === null) {
            return $default;
        }

        $value = $entry['value'];

        if (! $entry['translatable'] || ! is_array($value)) {
            return $value ?? $default;
        }

        $locale ??= app()->getLocale();

        return $value[$locale]
            ?? $value[config('app.fallback_locale')]
            ?? collect($value)->first(fn ($v) => filled($v))
            ?? $default;
    }

    public function raw(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key]['value'] ?? $default;
    }

    public function set(string $key, mixed $value, array $attributes = []): Setting
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            array_merge(['value' => $value], $attributes),
        );

        $this->flush();

        return $setting;
    }

    public function flush(): void
    {
        $this->cache = null;
        Cache::forget(self::CACHE_KEY);
    }
}

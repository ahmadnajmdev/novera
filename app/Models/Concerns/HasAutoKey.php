<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills the `key` column on creation when nobody supplied one.
 *
 * The key is a plumbing detail — it is how menus and page sections point at a
 * record — but it used to be a required field on every form, which meant an
 * editor adding a project had to invent a hyphenated identifier before they
 * could save. Now it is derived from the name and only shown under "Advanced".
 */
trait HasAutoKey
{
    public static function bootHasAutoKey(): void
    {
        static::creating(function ($model) {
            if (filled($model->key)) {
                return;
            }

            $model->key = $model->generateKey();
        });
    }

    public function generateKey(): string
    {
        $source = collect($this->keySourceAttributes())
            ->map(fn (string $attribute) => $this->plainAttribute($attribute))
            ->first(fn (?string $value) => filled($value));

        $base = Str::slug((string) $source) ?: Str::lower(class_basename($this));
        $key = $base;
        $suffix = 2;

        while (static::query()->where('key', $key)->exists()) {
            $key = $base.'-'.$suffix++;
        }

        return $key;
    }

    /** Attributes to derive the key from, best first. */
    protected function keySourceAttributes(): array
    {
        return ['name', 'title', 'label'];
    }

    /**
     * Translatable attributes are arrays at this point, so take whichever
     * language the editor actually typed into.
     */
    protected function plainAttribute(string $attribute): ?string
    {
        $value = $this->getAttributes()[$attribute] ?? null;

        if (is_string($value) && str_starts_with($value, '{')) {
            $value = json_decode($value, true) ?: $value;
        }

        if (is_array($value)) {
            $value = collect($value)->first(fn ($item) => filled($item));
        }

        return is_string($value) ? $value : null;
    }
}

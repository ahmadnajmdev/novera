<?php

namespace App\Filament\Support;

use App\Models\Locale;
use Closure;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Builds a per-locale tab set for translatable JSON columns.
 *
 * Locales come from the database, so adding a language in the CMS immediately
 * adds a tab to every content form — no resource has to be touched.
 *
 * Pass every translatable field of a form through a single call. One tab set
 * holding many fields reads as "this form, in Arabic"; one tab set per field
 * turns a form into a stack of identical widgets.
 */
class Translatable
{
    /**
     * @param  Closure(string $locale, bool $isDefault): array  $fields
     * @param  int  $columns  Grid columns inside each locale tab.
     */
    public static function tabs(Closure $fields, ?string $label = null, int $columns = 1): Tabs
    {
        $locales = static::locales();

        return Tabs::make($label ?? 'Content')
            ->tabs($locales->map(fn (Locale $locale) => Tab::make(static::tabLabel($locale))
                ->schema($fields($locale->code, (bool) $locale->is_default))
                ->columns($columns)
                ->badge(fn () => $locale->is_default ? 'default' : null)
            )->all())
            ->columnSpanFull()
            // Shared key on purpose: choosing Arabic once switches every
            // locale tab set on the page. It must not collide with the
            // outer tab sets, which use their own keys.
            ->persistTabInQueryString('locale');
    }

    /** @return \Illuminate\Support\Collection<int, Locale> */
    /**
     * English's native name in this data set is literally "EN", which made the
     * tab read "EN · EN". Show the language's name where it adds something.
     */
    protected static function tabLabel(Locale $locale): string
    {
        $code = strtoupper($locale->code);
        $native = trim((string) $locale->native_name);
        $name = strcasecmp($native, $code) === 0 ? $locale->name : $native;

        return $name.' · '.$code;
    }

    public static function locales()
    {
        static $cached = null;

        return $cached ??= Locale::active()->get();
    }

    public static function defaultCode(): string
    {
        return static::locales()->firstWhere('is_default', true)?->code
            ?? static::locales()->first()?->code
            ?? 'en';
    }

    public static function codes(): array
    {
        return static::locales()->pluck('code')->all();
    }
}

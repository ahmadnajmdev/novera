<?php

namespace App\Support;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

/**
 * Content-string translator.
 *
 * The site authors every string in the default locale and stores the Kurdish
 * and Arabic renderings against the English source, mirroring the dictionary
 * shipped with the design. Lookups try an exact match first, then fall back to
 * regex patterns so generated strings ("Ideas we return to for the kitchen")
 * translate without an entry per concept.
 */
class SiteTranslator
{
    public const CACHE_KEY = 'novera.translations';

    protected ?array $cache = null;

    protected function dictionary(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        return $this->cache = Cache::rememberForever(self::CACHE_KEY, function () {
            $exact = [];
            $lower = [];
            $patterns = [];

            foreach (Translation::all() as $translation) {
                if ($translation->is_pattern) {
                    $patterns[] = [
                        'source' => $translation->source,
                        'values' => $translation->values ?? [],
                    ];

                    continue;
                }

                if ($translation->group === 'lowercase') {
                    $lower[$translation->source] = $translation->values ?? [];

                    continue;
                }

                $exact[$translation->source] = $translation->values ?? [];
            }

            return compact('exact', 'lower', 'patterns');
        });
    }

    public function get(?string $source, ?string $locale = null): ?string
    {
        if ($source === null) {
            return null;
        }

        $locale ??= app()->getLocale();
        $key = trim($source);

        if ($key === '' || $locale === config('app.fallback_locale')) {
            return $source;
        }

        $dictionary = $this->dictionary();

        // The shipped dictionary lists a few words in both the exact and the
        // lowercase-substitution maps; either is a valid direct hit.
        $direct = $dictionary['exact'][$key][$locale] ?? $dictionary['lower'][$key][$locale] ?? null;

        if (filled($direct)) {
            return str_replace($key, $direct, $source);
        }

        foreach ($dictionary['patterns'] as $pattern) {
            $regex = '/'.str_replace('/', '\/', $pattern['source']).'/u';

            if (! preg_match($regex, $key, $matches)) {
                continue;
            }

            $out = $pattern['values'][$locale] ?? null;

            if (blank($out)) {
                continue;
            }

            for ($group = 1; $group < count($matches); $group++) {
                $raw = $matches[$group];
                $substitute = $dictionary['exact'][$raw][$locale]
                    ?? $dictionary['lower'][$raw][$locale]
                    ?? $raw;

                $out = str_replace('$'.$group, $substitute, $out);
            }

            return $out;
        }

        return $source;
    }

    /** Record a source string so untranslated copy surfaces in the CMS. */
    public function remember(?string $source, string $group = 'site'): void
    {
        $key = trim((string) $source);

        if ($key === '' || ! config('novera.collect_strings')) {
            return;
        }

        Translation::firstOrCreate(
            ['source_hash' => Translation::hashFor($key)],
            ['source' => $key, 'group' => $group, 'values' => []],
        );
    }

    public function flush(): void
    {
        $this->cache = null;
        Cache::forget(self::CACHE_KEY);
    }
}

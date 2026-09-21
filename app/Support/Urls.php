<?php

namespace App\Support;

use App\Models\Concept;
use App\Models\Material;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * URL building for a site whose slugs are editable content.
 *
 * Nothing here uses named routes with baked-in segments: a page's path is
 * whatever its translated slug says today.
 */
class Urls
{
    public function __construct(protected Site $site) {}

    protected function pageSlug(string $key, string $locale): ?string
    {
        $slugs = Cache::rememberForever(
            'novera.page-slugs',
            fn () => Page::all()->mapWithKeys(fn (Page $page) => [$page->key => $page->getTranslations('slug')])->all(),
        );

        if (! isset($slugs[$key])) {
            return null;
        }

        $translations = $slugs[$key];

        return $translations[$locale]
            ?? $translations[config('app.fallback_locale')]
            ?? collect($translations)->first();
    }

    public function page(string|Page|null $page, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $key = $page instanceof Page ? $page->key : $page;

        if ($key === null) {
            return url('/'.$locale);
        }

        $slug = $page instanceof Page
            ? ($page->getTranslation('slug', $locale) ?: $this->pageSlug($key, $locale))
            : $this->pageSlug($key, $locale);

        return $slug === null || $slug === ''
            ? url('/'.$locale)
            : url('/'.$locale.'/'.ltrim($slug, '/'));
    }

    /** Detail URL for a concept, project or material. */
    public function entity(Model $model, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $parent = match (true) {
            $model instanceof Concept => 'concepts',
            $model instanceof Project => 'projects',
            $model instanceof Material => 'materials',
            default => null,
        };

        if ($parent === null) {
            return url('/'.$locale);
        }

        $slug = $model->getTranslation('slug', $locale) ?: $model->key;

        return rtrim($this->page($parent, $locale), '/').'/'.$slug;
    }

    /** The same page in a different locale, for the language switcher. */
    public function alternate(string $locale): string
    {
        $current = request()->attributes->get('nv.page');
        $entity = request()->attributes->get('nv.entity');

        if ($entity instanceof Model) {
            return $this->entity($entity, $locale);
        }

        if ($current instanceof Page) {
            return $this->page($current, $locale);
        }

        return url('/'.$locale);
    }

    public function flush(): void
    {
        Cache::forget('novera.page-slugs');
    }
}

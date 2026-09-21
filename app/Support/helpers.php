<?php

use App\Support\Settings;
use App\Support\Site;
use App\Support\SiteTranslator;

if (! function_exists('nv_t')) {
    /** Translate an English source string through the CMS dictionary. */
    function nv_t(?string $source, ?string $locale = null): string
    {
        return app(SiteTranslator::class)->get($source, $locale) ?? '';
    }
}

if (! function_exists('nv_setting')) {
    function nv_setting(string $key, mixed $default = null): mixed
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('nv_site')) {
    function nv_site(): Site
    {
        return app(Site::class);
    }
}

if (! function_exists('nv_media')) {
    /** Resolve a media model, id or plain URL to a usable src string. */
    function nv_media(mixed $media, ?string $fallback = null): ?string
    {
        if ($media === null) {
            return $fallback;
        }

        if (is_string($media)) {
            return $media;
        }

        if ($media instanceof \App\Models\Media) {
            return $media->url() ?: $fallback;
        }

        if (is_numeric($media)) {
            return \App\Models\Media::find($media)?->url() ?: $fallback;
        }

        return $fallback;
    }
}

if (! function_exists('nv_route')) {
    /** Build a locale-aware front-end URL. */
    function nv_route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route($name, array_merge(['locale' => $locale], $parameters));
    }
}

if (! function_exists('nv_url')) {
    /** URL for a CMS page, by key or model. */
    function nv_url(string|\App\Models\Page|null $page = null, ?string $locale = null): string
    {
        return app(\App\Support\Urls::class)->page($page, $locale);
    }
}

if (! function_exists('nv_entity_url')) {
    /** Detail URL for a concept, project or material. */
    function nv_entity_url(\Illuminate\Database\Eloquent\Model $model, ?string $locale = null): string
    {
        return app(\App\Support\Urls::class)->entity($model, $locale);
    }
}

if (! function_exists('nv_alternate_url')) {
    /** The current page in another locale. */
    function nv_alternate_url(string $locale): string
    {
        return app(\App\Support\Urls::class)->alternate($locale);
    }
}

if (! function_exists('nv_img')) {
    /**
     * Sized image URL. Remote placeholders accept width/height parameters, so
     * the theme can ask for the crop it needs the way the design did.
     */
    function nv_img(mixed $media, int $width = 1200, ?int $height = null, ?string $fallback = null): ?string
    {
        $url = nv_media($media, $fallback);

        if (blank($url)) {
            return null;
        }

        if (! str_starts_with($url, config('novera.image.placeholder_host'))) {
            return $url;
        }

        $query = ['auto' => 'format', 'fit' => 'crop', 'q' => 80, 'w' => $width];

        if ($height !== null) {
            $query['h'] = $height;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').http_build_query($query);
    }
}

if (! function_exists('nv_accent')) {
    /**
     * Split a heading on its "*accent*" marker and translate each fragment
     * separately — the dictionary is keyed by exactly these fragments.
     */
    function nv_accent(?string $heading, ?string $locale = null): string
    {
        if (blank($heading)) {
            return '';
        }

        $parts = preg_split('/\*(.+?)\*/u', $heading, -1, PREG_SPLIT_DELIM_CAPTURE);
        $html = '';

        foreach ($parts as $index => $part) {
            if ($part === '') {
                continue;
            }

            $lead = str_starts_with($part, ' ') ? ' ' : '';
            $trail = str_ends_with($part, ' ') ? ' ' : '';
            $text = e(nv_t(trim($part), $locale));

            $html .= $index % 2 === 1
                ? '<em class="nv-accent">'.$text.'</em>'
                : $lead.$text.$trail;
        }

        return $html;
    }
}

if (! function_exists('nv_tr')) {
    /** Read a translatable model attribute with a default-locale fallback. */
    function nv_tr(?\Illuminate\Database\Eloquent\Model $model, string $attribute, ?string $locale = null): string
    {
        if ($model === null) {
            return '';
        }

        $locale ??= app()->getLocale();
        $translations = method_exists($model, 'getTranslations')
            ? $model->getTranslations($attribute)
            : (array) $model->{$attribute};

        $value = $translations[$locale] ?? null;

        if (filled($value)) {
            return $value;
        }

        // Not translated yet: fall back to the source language and run it
        // through the dictionary so seeded English still reads correctly.
        $source = $translations[config('app.fallback_locale')]
            ?? collect($translations)->first(fn ($item) => filled($item))
            ?? '';

        return nv_t($source, $locale);
    }
}

if (! function_exists('nv_edit')) {
    /**
     * Marks a node as editable in the visual editor. Rendered always (it is
     * inert unless the body carries .nv-editing), so the front end has exactly
     * one DOM shape whether or not an editor is looking at it.
     */
    function nv_edit(string $model, int|string $id, string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return 'data-nv-edit="'.e($model.':'.$id.':'.$field.':'.$locale).'"';
    }
}

if (! function_exists('nv_media_edit')) {
    /**
     * Marks a picture as swappable in the visual editor. Media is not
     * translated, so unlike nv_edit() this carries no locale.
     */
    function nv_media_edit(string $model, int|string $id, string $field): string
    {
        return 'data-nv-media="'.e($model.':'.$id.':'.$field).'"';
    }
}

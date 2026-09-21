<?php

namespace App\Support;

use App\Models\ContentCollection;
use App\Models\ContentItem;
use App\Models\Locale;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Keeps the few cross-request caches honest.
 *
 * Everything an editor can change is cached somewhere; without this a saved
 * setting or renamed slug would only appear after a manual cache clear.
 */
class CacheFlusher
{
    /** model => the caches its changes invalidate */
    public const WATCHED = [
        Setting::class => 'settings',
        Translation::class => 'translations',
        Page::class => 'urls',
        Locale::class => 'site',
        Menu::class => 'site',
        MenuItem::class => 'site',
        ContentCollection::class => 'site',
        ContentItem::class => 'site',
        Redirect::class => 'redirects',
    ];

    public static function register(): void
    {
        foreach (self::WATCHED as $model => $target) {
            $handler = fn (Model $record) => self::flush($target);

            $model::saved($handler);
            $model::deleted($handler);
        }
    }

    public static function flush(string $target): void
    {
        match ($target) {
            'settings' => app(Settings::class)->flush(),
            'translations' => app(SiteTranslator::class)->flush(),
            'urls' => app(Urls::class)->flush(),
            'redirects' => Cache::forget('novera.redirects'),
            'site' => self::flushAll(),
            default => null,
        };
    }

    public static function flushAll(): void
    {
        app(Settings::class)->flush();
        app(SiteTranslator::class)->flush();
        app(Urls::class)->flush();
        app(Site::class)->flush();
        Cache::forget('novera.redirects');
    }
}

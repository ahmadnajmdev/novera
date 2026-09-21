<?php

namespace App\Support;

use App\Models\ContentCollection;
use App\Models\Locale;
use App\Models\Menu;
use Illuminate\Support\Collection;

/**
 * Read-side facade for everything the public theme needs: locales, menus,
 * design tokens and the generic content collections.
 *
 * Results are memoised per request rather than pushed into the shared cache:
 * these are small tables, and caching hydrated Eloquent models across
 * processes invites stale or unserialisable payloads.
 */
class Site
{
    protected ?Collection $locales = null;

    /** @var array<string, Collection> */
    protected array $menus = [];

    /** @var array<string, Collection> */
    protected array $collections = [];

    public function __construct(protected Settings $settings) {}

    public function locales(): Collection
    {
        return $this->locales ??= Locale::active()->get();
    }

    public function defaultLocale(): ?Locale
    {
        return $this->locales()->firstWhere('is_default', true) ?? $this->locales()->first();
    }

    public function locale(?string $code = null): ?Locale
    {
        $code ??= app()->getLocale();

        return $this->locales()->firstWhere('code', $code);
    }

    public function direction(?string $code = null): string
    {
        return $this->locale($code)?->direction ?? 'ltr';
    }

    public function isRtl(?string $code = null): bool
    {
        return $this->direction($code) === 'rtl';
    }

    public function menu(string $key): Collection
    {
        return $this->menus[$key] ??= Menu::where('key', $key)->first()
            ?->allItems()->where('is_visible', true)->get()
            ?? collect();
    }

    public function collection(string $key): Collection
    {
        return $this->collections[$key] ??= ContentCollection::where('key', $key)->first()
            ?->activeItems()->with('media')->get()
            ?? collect();
    }

    /**
     * Design tokens. Every colour, radius and font stack the theme paints with
     * comes from here, so the palette is editable in the CMS rather than
     * compiled into the stylesheet.
     */
    public function tokens(): array
    {
        return [
            'ink' => $this->settings->get('theme.ink', '#0B0F22'),
            'navy' => $this->settings->get('theme.navy', '#131936'),
            'gold' => $this->settings->get('theme.gold', '#F2D9A0'),
            'gold_deep' => $this->settings->get('theme.gold_deep', '#8A6A16'),
            'light' => $this->settings->get('theme.light', '#EEF3F9'),
            'body' => $this->settings->get('theme.body', '#39415F'),
            'muted' => $this->settings->get('theme.muted', '#6C7490'),
            'white' => $this->settings->get('theme.white', '#FFFFFF'),
            'radius' => $this->settings->get('theme.radius', '16px'),
            'max_width' => $this->settings->get('theme.max_width', '1680px'),
            'heading_font' => $this->settings->get('theme.heading_font', "'Cinzel', Georgia, serif"),
            'body_font' => $this->settings->get('theme.body_font', "'Hanken Grotesk', system-ui, sans-serif"),
            'arabic_heading_font' => $this->settings->get('theme.arabic_heading_font', "'Novera Arabic', 'Cinzel', serif"),
            'arabic_body_font' => $this->settings->get('theme.arabic_body_font', "'Noto Naskh Arabic', sans-serif"),
        ];
    }

    public function motion(): array
    {
        return [
            'reveal' => (bool) $this->settings->get('motion.reveal', true),
            'parallax' => (bool) $this->settings->get('motion.parallax', true),
            'smooth_scroll' => (bool) $this->settings->get('motion.smooth_scroll', true),
            'marquee' => (bool) $this->settings->get('motion.marquee', true),
            'boot' => (bool) $this->settings->get('motion.boot', true),
            'rail_autoscroll' => (bool) $this->settings->get('motion.rail_autoscroll', true),
        ];
    }

    public function flush(): void
    {
        $this->locales = null;
        $this->menus = [];
        $this->collections = [];
    }
}

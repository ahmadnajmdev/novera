<?php

namespace App\Filament\Support;

use App\Models\Page;
use App\Models\Section;

/**
 * Starting points offered when a page is created.
 *
 * Creating a page used to leave you staring at an empty Sections table with
 * no idea what to put in it. Picking a layout builds the usual arrangement
 * up front, filled with the page's own name, so the very next thing you do is
 * rewrite words that are already on screen.
 */
class PageLayouts
{
    public static function all(): array
    {
        return [
            'standard' => [
                'label' => 'A normal page',
                'hint' => 'A banner with the page name, a paragraph beside a photo, and a “get in touch” band at the bottom. Use this unless you know you want something else.',
                'icon' => 'heroicon-o-document-text',
                'sections' => ['hero.plain', 'intro.split', 'cta.banner'],
            ],
            'showcase' => [
                'label' => 'A page that shows off work',
                'hint' => 'A banner, then the full portfolio with its filter buttons, then a “get in touch” band.',
                'icon' => 'heroicon-o-squares-2x2',
                'sections' => ['hero.plain', 'projects.grid', 'cta.banner'],
            ],
            'contact' => [
                'label' => 'A page people write to you from',
                'hint' => 'A banner, then the enquiry form beside your phone, address and opening hours.',
                'icon' => 'heroicon-o-envelope',
                'sections' => ['hero.plain', 'contact.form'],
            ],
            'blank' => [
                'label' => 'Start empty',
                'hint' => 'No sections at all. Pick this only if you want to build the page piece by piece.',
                'icon' => 'heroicon-o-minus',
                'sections' => [],
            ],
        ];
    }

    public static function default(): string
    {
        return 'standard';
    }

    public static function sections(string $layout): array
    {
        return static::all()[$layout]['sections'] ?? [];
    }

    /**
     * Build the layout's sections on a freshly created page.
     *
     * Everything written here is a placeholder the editor is meant to
     * replace, so it is phrased as an instruction rather than left blank —
     * an empty heading renders as an empty band and reads as a broken page.
     */
    public static function apply(Page $page, string $layout, string $locale): void
    {
        $title = nv_tr($page, 'title', $locale) ?: 'New page';

        foreach (static::sections($layout) as $sort => $type) {
            Section::create([
                'page_id' => $page->id,
                'type' => $type,
                'sort' => $sort,
                'is_visible' => true,
                'data' => static::dataFor($type, $title, $locale),
                'settings' => static::settingsFor($type),
            ]);
        }
    }

    protected static function dataFor(string $type, string $title, string $locale): array
    {
        $wrap = fn (string $value) => [$locale => $value];

        return match ($type) {
            'hero.plain' => [
                'heading' => $wrap($title),
                'intro' => $wrap('Write a sentence or two introducing this page.'),
            ],
            'intro.split' => [
                'number' => $wrap('01'),
                'heading' => $wrap('A heading for this section'),
                'body' => $wrap('Replace this with what you want to say here.'),
            ],
            'projects.grid' => [],
            'contact.form' => [
                'eyebrow' => $wrap('Get in touch'),
            ],
            'cta.banner' => [
                'heading' => $wrap('Ready to *begin*?'),
                'button_label' => $wrap('Contact us'),
                'button_page' => 'contact',
            ],
            default => [],
        };
    }

    protected static function settingsFor(string $type): array
    {
        $definition = SectionBlocks::definitions()[$type] ?? [];
        $settings = [];

        if (! empty($definition['collection'])) {
            $settings['collection'] = $definition['collection'];
        }

        foreach ($definition['settings'] ?? [] as $key => $label) {
            $settings[$key] = true;
        }

        return $settings;
    }
}

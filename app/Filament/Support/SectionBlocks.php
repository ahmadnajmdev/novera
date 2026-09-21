<?php

namespace App\Filament\Support;

use App\Models\ContentCollection;
use App\Models\Form;
use App\Models\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section as FormSection;

/**
 * Registry of page-builder block types.
 *
 * Each entry declares a label, the translatable text fields it owns and any
 * non-translatable settings, so the CMS form for a block is derived rather
 * than hand-written per type. Adding a block means adding an entry here plus
 * a matching Blade view under resources/views/site/sections.
 */
class SectionBlocks
{
    /**
     * label:      what an editor calls this section
     * hint:       one line describing what it looks like on the page
     * group:      heading it sits under in the section picker
     * icon:       shown on its card in the picker
     * text:       translatable string fields (rendered per locale)
     * area:       translatable multi-line fields
     * media:      media picker fields stored on data
     * pages:      "links to" fields — a page picker, stored as the page key
     * forms:      a form picker, stored as the form key
     * collection: the default list key this block reads its rows from
     * settings:   on/off switches
     */
    public static function definitions(): array
    {
        return [
            'hero.video' => [
                'label' => 'Big opening banner with video',
                'group' => 'Page openers',
                'icon' => 'heroicon-o-film',
                'hint' => 'Full-screen video with a headline over it. Usually only on the home page.',
                'text' => [
                    'eyebrow' => 'Small line above the headline',
                    'heading' => 'Headline',
                    'primary_label' => 'Main button text',
                    'secondary_label' => 'Second link text',
                ],
                'area' => ['lead' => 'Opening paragraph'],
                'pages' => ['primary_page' => 'Main button goes to', 'secondary_page' => 'Second link goes to'],
                'settings' => ['marquee' => 'Show the scrolling word strip', 'scroll_hint' => 'Show the "scroll down" arrow'],
            ],
            'hero.image' => [
                'label' => 'Opening banner with a photo',
                'group' => 'Page openers',
                'icon' => 'heroicon-o-photo',
                'hint' => 'A headline over a single photo, used at the top of inner pages.',
                'text' => ['eyebrow' => 'Small line above the headline', 'heading' => 'Headline'],
                'media' => ['media_id' => 'Background photo'],
                'plainText' => ['height' => 'Height (leave empty for the standard size)'],
            ],
            'hero.plain' => [
                'label' => 'Opening banner, text only',
                'group' => 'Page openers',
                'icon' => 'heroicon-o-bars-3-bottom-left',
                'hint' => 'A headline and paragraph on a plain colour, with no photo.',
                'text' => ['eyebrow' => 'Small line above the headline', 'heading' => 'Headline'],
                'area' => ['intro' => 'Opening paragraph'],
                'background' => true,
            ],
            'intro.split' => [
                'label' => 'Text beside a photo',
                'group' => 'Words and pictures',
                'icon' => 'heroicon-o-view-columns',
                'hint' => 'A paragraph on one side, a photo on the other, with figures underneath.',
                'text' => ['number' => 'Step number (01, 02 …)', 'heading' => 'Headline'],
                'area' => ['body' => 'Paragraph', 'caption' => 'Caption under the photo'],
                'media' => ['media_id' => 'Photo'],
                'collection' => 'stats',
            ],
            'projects.featured' => [
                'label' => 'Projects — highlight strip',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-star',
                'hint' => 'A short row of recent projects with a link to see them all.',
                'text' => ['number' => 'Step number (01, 02 …)', 'heading' => 'Headline', 'link_label' => 'Link text'],
                'pages' => ['link_page' => 'Link goes to'],
                'settingsNumber' => ['limit' => 'How many projects to show'],
            ],
            'concepts.rail' => [
                'label' => 'Rooms — sliding row',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-arrows-right-left',
                'hint' => 'Room types in a row the visitor can drag sideways.',
                'text' => [
                    'number' => 'Step number (01, 02 …)',
                    'heading' => 'Headline',
                    'hint' => 'Small hint text',
                    'cta_label' => 'Button text',
                ],
                'area' => ['body' => 'Paragraph'],
                'pages' => ['cta_page' => 'Button goes to'],
            ],
            'concepts.grid' => [
                'label' => 'Rooms — full grid',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-squares-plus',
                'hint' => 'Every room type as a grid of cards. Nothing to fill in — it uses the Rooms list.',
            ],
            'materials.index' => [
                'label' => 'Materials — hover list',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-swatch',
                'hint' => 'Material names that reveal a photo as the visitor points at them.',
                'text' => ['number' => 'Step number (01, 02 …)', 'heading' => 'Headline', 'cta_label' => 'Button text'],
                'area' => ['body' => 'Paragraph'],
                'pages' => ['cta_page' => 'Button goes to'],
            ],
            'materials.groups' => [
                'label' => 'Materials — grouped grid',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-rectangle-stack',
                'hint' => 'All materials, sorted into their groups. Nothing to fill in.',
            ],
            'showroom.feature' => [
                'label' => 'Showroom and factory',
                'group' => 'Words and pictures',
                'icon' => 'heroicon-o-rectangle-group',
                'hint' => 'A wide photo with a stack of small cards beside it.',
                'text' => [
                    'number' => 'Step number (01, 02 …)',
                    'heading' => 'Headline',
                    'card_eyebrow' => 'Small line on the card',
                ],
                'area' => ['card_body' => 'Card paragraph'],
                'media' => ['wide_media_id' => 'Wide photo'],
                'cards' => true,
            ],
            'services.compact' => [
                'label' => 'Services — short list',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-list-bullet',
                'hint' => 'Service names with a line each, taken from the Services screen.',
                'text' => ['number' => 'Step number (01, 02 …)', 'heading' => 'Headline'],
            ],
            'services.full' => [
                'label' => 'Services — full list',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'hint' => 'Every service in full, with its bullet points. Nothing to fill in.',
            ],
            'projects.grid' => [
                'label' => 'Projects — all, with filters',
                'group' => 'Show your work',
                'icon' => 'heroicon-o-squares-2x2',
                'hint' => 'The complete portfolio, with buttons to narrow it down.',
                'settings' => ['filters' => 'Show the filter buttons'],
            ],
            'about.story' => [
                'label' => 'About — story and standards',
                'group' => 'Words and pictures',
                'icon' => 'heroicon-o-book-open',
                'hint' => 'Two paragraphs about the company, followed by a list of standards.',
                'text' => [
                    'eyebrow' => 'Small line above the headline',
                    'heading' => 'Headline',
                    'standards_eyebrow' => 'Small line above the standards',
                ],
                'area' => ['body_one' => 'First paragraph', 'body_two' => 'Second paragraph'],
                'collection' => 'standards',
            ],
            'about.facilities' => [
                'label' => 'About — facilities',
                'group' => 'Words and pictures',
                'icon' => 'heroicon-o-building-storefront',
                'hint' => 'A wide photo with the facilities list beside it.',
                'media' => ['media_id' => 'Wide photo'],
                'collection' => 'facilities',
            ],
            'cta.banner' => [
                'label' => 'Closing "get in touch" banner',
                'group' => 'Endings',
                'icon' => 'heroicon-o-megaphone',
                'hint' => 'The band at the bottom of a page with one big button.',
                'text' => ['heading' => 'Headline', 'button_label' => 'Button text'],
                'pages' => ['button_page' => 'Button goes to'],
                'media' => ['media_id' => 'Background photo'],
            ],
            'contact.form' => [
                'label' => 'Contact form and details',
                'group' => 'Endings',
                'icon' => 'heroicon-o-envelope',
                'hint' => 'The enquiry form beside your phone, address and opening hours.',
                'text' => ['eyebrow' => 'Small line above the headline'],
                'media' => ['media_id' => 'Map picture'],
                'forms' => ['form' => 'Which form to show'],
                'collection' => 'contact_rows',
            ],
        ];
    }

    /**
     * Pages offered as button destinations, keyed by the value that gets
     * stored. Named so it can be asserted on without mounting a form.
     *
     * @return array<string, string>
     */
    public static function pageOptions(): array
    {
        return Page::orderBy('sort')->get()
            ->mapWithKeys(fn (Page $page) => [
                $page->key => nv_tr($page, 'title', Translatable::defaultCode()),
            ])
            ->all();
    }

    /**
     * The section types shaped for the card picker: grouped, described and
     * iconed, so the choice can be made by reading rather than by knowing.
     *
     * @return array<string, array<string, string>>
     */
    public static function cards(): array
    {
        return collect(static::definitions())
            ->map(fn (array $definition) => [
                'label' => $definition['label'],
                'hint' => $definition['hint'] ?? '',
                'icon' => $definition['icon'] ?? 'heroicon-o-square-3-stack-3d',
                'group' => $definition['group'] ?? 'Other',
            ])
            ->all();
    }

    public static function options(): array
    {
        return collect(static::definitions())
            ->map(fn (array $definition) => $definition['label'])
            ->all();
    }

    /** Short description shown under the section picker. */
    public static function hint(?string $type): ?string
    {
        return static::definitions()[$type]['hint'] ?? null;
    }

    public static function label(?string $type): string
    {
        return static::definitions()[$type]['label'] ?? ($type ?? 'Unknown section');
    }

    /** Form schema for one block's editable content. */
    public static function schema(?string $type): array
    {
        $definition = static::definitions()[$type] ?? null;

        if ($definition === null) {
            return [];
        }

        $components = [];

        if (! empty($definition['text']) || ! empty($definition['area'])) {
            $components[] = Translatable::tabs(function (string $locale, bool $isDefault) use ($definition) {
                $fields = [];

                foreach ($definition['text'] ?? [] as $key => $label) {
                    $fields[] = TextInput::make("data.{$key}.{$locale}")
                        ->label($label)
                        ->required($isDefault && in_array($key, ['heading'], true))
                        ->helperText($key === 'heading' ? static::HEADING_HINT : null);
                }

                foreach ($definition['area'] ?? [] as $key => $label) {
                    $fields[] = Textarea::make("data.{$key}.{$locale}")->label($label)->rows(3);
                }

                return $fields;
            }, 'Wording');
        }

        $links = [];

        foreach ($definition['media'] ?? [] as $key => $label) {
            $links[] = MediaPicker::make("data.{$key}", $label);
        }

        // Buttons point at a page, chosen from a list. The stored value is the
        // page's key, so the link keeps working when someone renames the page
        // or its address — and nobody has to know what a key is.
        foreach ($definition['pages'] ?? [] as $key => $label) {
            $links[] = Select::make("data.{$key}")
                ->label($label)
                ->options(fn () => static::pageOptions())
                ->searchable()
                ->native(false)
                ->placeholder('Nowhere — hide this button');
        }

        foreach ($definition['forms'] ?? [] as $key => $label) {
            $links[] = Select::make("data.{$key}")
                ->label($label)
                ->options(fn () => Form::orderBy('name')->pluck('name', 'key')->all())
                ->native(false)
                ->helperText('Set the questions it asks under Menus & forms → Forms.');
        }

        foreach ($definition['plainText'] ?? [] as $key => $label) {
            $links[] = TextInput::make("settings.{$key}")->label($label);
        }

        if ($links !== []) {
            $components[] = FormSection::make('Pictures and links')->schema($links)->columns(2);
        }

        $settings = [];

        foreach ($definition['settings'] ?? [] as $key => $label) {
            $settings[] = Toggle::make("settings.{$key}")->label($label)->default(true);
        }

        foreach ($definition['settingsNumber'] ?? [] as $key => $label) {
            $settings[] = TextInput::make("settings.{$key}")->label($label)->numeric()->minValue(1);
        }

        if (! empty($definition['background'])) {
            $settings[] = Select::make('settings.background')
                ->label('Background colour')
                ->options(['white' => 'White', 'ink' => 'Near-black', 'navy' => 'Navy', 'light' => 'Pale blue'])
                ->native(false)
                ->default('white');
        }

        if (! empty($definition['collection'])) {
            $settings[] = Select::make('settings.collection')
                ->label('Which list fills this section')
                ->options(fn () => ContentCollection::orderBy('name')->pluck('name', 'key')->all())
                ->default($definition['collection'])
                ->native(false)
                ->helperText('Edit the rows themselves under Media & lists → Lists.');
        }

        if ($settings !== []) {
            $components[] = FormSection::make('Options')->schema($settings)->columns(2);
        }

        if (! empty($definition['cards'])) {
            $components[] = Repeater::make('data.cards')
                ->label('Cards')
                ->schema([
                    Translatable::tabs(fn (string $locale) => [
                        TextInput::make("title.{$locale}")->label('Card title'),
                        Textarea::make("body.{$locale}")->label('Card text')->rows(3),
                    ], 'Wording'),
                    MediaPicker::make('media_id', 'Card photo'),
                ])
                ->addActionLabel('Add a card')
                ->defaultItems(0)
                ->reorderable()
                ->collapsible()
                ->columnSpanFull();
        }

        return $components;
    }

    /**
     * The one piece of syntax an editor has to learn, so it is spelled out
     * everywhere a headline is typed rather than described once in a manual.
     */
    public const HEADING_HINT = 'Put *asterisks* around the words you want in gold italics — for example: Interiors that *endure*.';
}

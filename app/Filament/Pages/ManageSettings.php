<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use App\Models\Setting;
use App\Support\Settings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * One screen for every site-wide value: brand, contact, palette, motion,
 * hero media and SEO defaults.
 *
 * The form is generated from the settings table itself, so a new setting row
 * appears here automatically with the right control for its type.
 */
class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Site settings';

    public function getSubheading(): ?string
    {
        return 'Things that appear on every page — your brand, colours and the wording search engines show.';
    }

    protected static ?int $navigationSort = 1;

    public array $data = [];

    /** Site-wide settings are limited to administrators. */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill($this->currentValues());
    }

    protected function currentValues(): array
    {
        return Setting::all()
            ->mapWithKeys(fn (Setting $setting) => [$this->fieldName($setting->key) => $setting->value])
            ->all();
    }

    /** Dots are path separators in Filament state, so store them flattened. */
    protected function fieldName(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    public function form(Schema $schema): Schema
    {
        $groups = Setting::orderBy('sort')->get()->groupBy('group');

        return $schema
            ->components([
                Tabs::make('Settings')
                    ->columnSpanFull()
                    // Distinct from the locale tabs nested inside, which use
                    // 'locale'; sharing one key made the two fight over it.
                    ->persistTabInQueryString('group')
                    ->tabs(
                        $groups->map(fn (Collection $settings, string $group) => Tab::make($this->titleFor($group))
                            ->icon($this->iconFor($group))
                            ->schema([
                                ...($this->hintFor($group)
                                    ? [Text::make($this->hintFor($group))->color('gray')->columnSpanFull()]
                                    : []),
                                ...$this->fieldsFor($settings),
                            ]))
                            ->values()
                            ->all(),
                    ),
            ])
            ->statePath('data');
    }

    /** Group names come from the database; these are what an editor calls them. */
    protected function titleFor(string $group): string
    {
        return match ($group) {
            'brand' => 'Brand',
            'contact' => 'Contact',
            'theme' => 'Colours & type',
            'motion' => 'Animation',
            'hero' => 'Home page video',
            'seo' => 'Google & sharing',
            default => Str::headline($group),
        };
    }

    protected function hintFor(string $group): ?string
    {
        return match ($group) {
            'brand' => 'Your name and logos, used in the header, the footer and on every shared link.',
            'contact' => 'Your phone number, email, address and opening hours are rows under '
                .'Media & lists → Lists → Contact details. What is here is the map and the line in the menu overlay.',
            'theme' => 'Change these and the whole website changes with them. '
                .'The colour swatches are safe to play with; the font boxes expect a font name.',
            'motion' => 'Switch off anything that feels busy. Visitors who ask their device for '
                .'reduced motion never see these regardless.',
            'seo' => 'What Google and social networks show when someone finds or shares the site.',
            default => null,
        };
    }

    protected function iconFor(string $group): string
    {
        return match ($group) {
            'brand' => 'heroicon-o-sparkles',
            'contact' => 'heroicon-o-map-pin',
            'theme' => 'heroicon-o-swatch',
            'motion' => 'heroicon-o-bolt',
            'hero' => 'heroicon-o-film',
            'seo' => 'heroicon-o-magnifying-glass',
            default => 'heroicon-o-adjustments-horizontal',
        };
    }

    /**
     * Two blocks per group: one tab set holding every translatable field in
     * the group, then a grid of the values that are the same in all languages.
     */
    protected function fieldsFor(Collection $settings): array
    {
        $translatable = $settings->where('is_translatable', true);
        $plain = $settings->where('is_translatable', false);

        $components = [];

        if ($translatable->isNotEmpty()) {
            $components[] = Translatable::tabs(
                fn (string $locale) => $translatable
                    ->map(fn (Setting $setting) => $this->translatableField($setting, $locale))
                    ->values()
                    ->all(),
                'Wording',
                columns: 2,
            );
        }

        if ($plain->isNotEmpty()) {
            $components[] = Section::make($translatable->isNotEmpty() ? 'The same in every language' : null)
                ->schema($plain->map(fn (Setting $setting) => $this->plainField($setting))->values()->all())
                ->columns($plain->contains(fn (Setting $s) => $s->type === 'color') ? 3 : 2);
        }

        return $components;
    }

    protected function translatableField(Setting $setting, string $locale)
    {
        $name = $this->fieldName($setting->key).'.'.$locale;
        $label = $this->labelFor($setting);

        return $setting->type === 'textarea'
            ? Textarea::make($name)->label($label)->rows(3)->helperText($setting->hint)->columnSpanFull()
            : TextInput::make($name)->label($label)->helperText($setting->hint);
    }

    protected function plainField(Setting $setting)
    {
        $name = $this->fieldName($setting->key);
        $label = $this->labelFor($setting);

        return match ($setting->type) {
            'color' => ColorPicker::make($name)->label($label)->helperText($setting->hint),
            'boolean' => Toggle::make($name)->label($label)->helperText($setting->hint)->inline(false),
            'textarea' => Textarea::make($name)->label($label)->rows(3)->helperText($setting->hint)->columnSpanFull(),
            'media' => MediaPicker::make($name, $label),
            default => TextInput::make($name)->label($label)->helperText($setting->hint),
        };
    }

    protected function labelFor(Setting $setting): string
    {
        return $setting->label ?: Str::headline(Str::afterLast($setting->key, '.'));
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach (Setting::all() as $setting) {
            $name = $this->fieldName($setting->key);

            if (! array_key_exists($name, $state)) {
                continue;
            }

            $value = $state[$name];

            if ($setting->type === 'boolean') {
                $value = (bool) $value;
            }

            $setting->update(['value' => $value]);
        }

        app(Settings::class)->flush();

        Notification::make()->success()->title('Settings saved')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Save changes')->action('save'),
        ];
    }
}

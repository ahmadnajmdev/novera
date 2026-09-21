<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Page')->columnSpanFull()->tabs([
                Tab::make('Name & address')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Translatable::tabs(fn (string $locale, bool $isDefault) => [
                            TextInput::make("title.{$locale}")
                                ->label('Page name')
                                ->helperText('What this page is called in menus and browser tabs.')
                                ->required($isDefault)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) use ($locale) {
                                    // Only auto-fill while the address is still empty, so an
                                    // existing published URL is never silently rewritten.
                                    if (blank($get("slug.{$locale}")) && filled($state)) {
                                        $set("slug.{$locale}", Str::slug($state));
                                    }
                                }),
                            TextInput::make("slug.{$locale}")
                                ->label('Web address')
                                ->prefix(fn () => rtrim(config('app.url'), '/').'/'.$locale.'/')
                                ->helperText('Filled in for you from the name. Leave empty for the home page. '
                                    .'Changing it changes the link people have already shared.')
                                ->rule('regex:/^[a-z0-9\-\/]*$/')
                                ->validationMessages([
                                    'regex' => 'Use lower-case letters, numbers and hyphens only — no spaces.',
                                ]),
                        ], 'Name & address'),
                    ]),

                Tab::make('Visibility')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Section::make()->schema([
                            // published_at is the stored truth; most people only
                            // want on or off, so the date only appears once they
                            // have said yes to publishing.
                            Toggle::make('published')
                                ->label('Published — visible to everyone')
                                ->helperText('Off keeps it a draft that only signed-in editors can see.')
                                ->live()
                                ->dehydrated(false)
                                ->afterStateHydrated(fn ($component, $record) => $component->state(
                                    (bool) $record?->published_at,
                                ))
                                ->afterStateUpdated(fn ($state, $set) => $set(
                                    'published_at', $state ? now() : null,
                                ))
                                ->columnSpanFull(),
                            DateTimePicker::make('published_at')
                                ->label('Goes live on')
                                ->seconds(false)
                                ->helperText('Leave as-is to publish now, or pick a future date and time.')
                                ->visible(fn (Get $get) => (bool) $get('published'))
                                // Hidden by an unticked toggle still has to save,
                                // otherwise unpublishing would never write the null.
                                ->dehydratedWhenHidden(),
                            Toggle::make('dark_hero')
                                ->label('Page starts with a dark banner')
                                ->helperText('Keeps the logo and menu light until the visitor scrolls down.'),
                        ])->columns(2),
                    ]),

                Tab::make('Search engines')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Translatable::tabs(fn (string $locale) => [
                            TextInput::make("seo_title.{$locale}")
                                ->label('Title in Google results')
                                ->helperText('Leave empty to use the page name.')
                                ->maxLength(70),
                            Textarea::make("seo_description.{$locale}")
                                ->label('Description in Google results')
                                ->helperText('One or two sentences, around 150 characters.')
                                ->rows(3)
                                ->maxLength(200),
                        ], 'Wording'),
                        Section::make()->schema([
                            MediaPicker::make('og_media_id', 'Picture used when the link is shared'),
                            Toggle::make('noindex')
                                ->label('Keep this page out of Google'),
                        ])->columns(2),
                    ]),

                Tab::make('Advanced')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->schema([
                        Section::make()
                            ->description('You will rarely need these.')
                            ->schema([
                                TextInput::make('key')
                                    ->label('Reference name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->disabled(fn ($record) => $record?->is_system)
                                    ->helperText('How menus and buttons point at this page. '
                                        .'Built-in pages keep theirs locked.'),
                                TextInput::make('sort')
                                    ->label('Position in lists')
                                    ->helperText('Lower numbers come first.')
                                    ->numeric()
                                    ->default(0),
                            ])->columns(2),
                    ]),
            ]),
        ]);
    }
}

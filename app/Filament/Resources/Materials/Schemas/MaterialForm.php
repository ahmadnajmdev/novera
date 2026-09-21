<?php

namespace App\Filament\Resources\Materials\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use App\Models\MaterialGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")
                    ->label('Name')
                    ->required($isDefault)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get) use ($locale) {
                        if (blank($get("slug.{$locale}")) && filled($state)) {
                            $set("slug.{$locale}", Str::slug($state));
                        }
                    }),
                TextInput::make("slug.{$locale}")
                    ->label('Web address')
                    ->helperText('Filled in from the name.')
                    ->rule('regex:/^[a-z0-9\-]*$/'),
                TextInput::make("tag.{$locale}")->label('Short label')->helperText('Sits beside the name, e.g. “Translucent”.'),
                Textarea::make("blurb.{$locale}")->label('Short description')->rows(2),
                Textarea::make("body.{$locale}")->label('Full description')->rows(5),
                TextInput::make("seo_title.{$locale}")->label('Title in Google results'),
                Textarea::make("seo_description.{$locale}")->label('Description in Google results')->rows(2),
            ]),

            Section::make('Details')->schema([
                Select::make('material_group_id')
                    ->label('Material group')
                    ->options(fn () => MaterialGroup::all()->mapWithKeys(fn ($g) => [$g->id => nv_tr($g, 'name', Translatable::defaultCode())]))
                    ->required()
                    ->native(false),
                MediaPicker::make('media_id', 'Main photo'),
                Toggle::make('is_active')
                    ->label('Published — visible on the website')
                    ->default(true),
            ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

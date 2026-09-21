<?php

namespace App\Filament\Resources\Concepts\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ConceptForm
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
                Textarea::make("blurb.{$locale}")->label('Short description')->rows(2),
                TextInput::make("seo_title.{$locale}")->label('Title in Google results'),
                Textarea::make("seo_description.{$locale}")->label('Description in Google results')->rows(2),
            ]),

            Section::make('Details')->schema([
                MediaPicker::make('media_id', 'Main photo'),
                Toggle::make('is_active')
                    ->label('Published — visible on the website')
                    ->default(true),
            ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

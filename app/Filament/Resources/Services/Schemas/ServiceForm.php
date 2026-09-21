<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")->label('Name')->required($isDefault),
                Textarea::make("body.{$locale}")->label('Description')->rows(4),
            ]),
            Section::make('Details')->schema([
                TextInput::make('number')
                    ->label('Stage number')
                    ->maxLength(8)
                    ->helperText('The large numeral beside it, e.g. 01.'),
                MediaPicker::make('media_id', 'Photo'),
                Toggle::make('is_active')
                    ->label('Published — visible on the website')
                    ->default(true),
            ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

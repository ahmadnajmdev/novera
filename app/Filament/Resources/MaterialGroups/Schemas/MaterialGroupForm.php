<?php

namespace App\Filament\Resources\MaterialGroups\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaterialGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")->label('Group name')->required($isDefault),
            ]),
            Section::make('Colours')
                ->description('Each group appears as its own band on the materials page. '
                    .'These set how that band looks.')
                ->schema([
                    ColorPicker::make('background')->label('Band background'),
                    ColorPicker::make('foreground')->label('Text on the band'),
                    ColorPicker::make('muted')->label('Fainter text'),
                    TextInput::make('line')
                        ->label('Dividing line')
                        ->helperText('A colour like rgba(19,25,54,.14). Leave as it is if unsure.'),
                ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

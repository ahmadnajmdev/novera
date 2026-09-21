<?php

namespace App\Filament\Resources\ConceptTabs\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConceptTabForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("label.{$locale}")->label('Tab label')->required($isDefault),
                TextInput::make("heading_template.{$locale}")->label('Heading above the tab')
                    ->helperText('Write :concept where the room name should go — '
                        .'“Ideas we return to for the :concept” becomes “…for the kitchen”.'),
            ]),
            Section::make()->schema([
                Select::make('source')
                    ->label('Where this tab gets its content')
                    ->required()
                    ->native(false)
                    ->options([
                        'items' => 'Whatever is typed on each room type',
                        'styles' => 'The shared list of design styles',
                    ])
                    ->default('items'),
                Toggle::make('is_visible')->label('Show this tab')->default(true),
            ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

<?php

namespace App\Filament\Resources\ContentCollections\Schemas;

use App\Filament\Support\Advanced;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentCollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->label('What to call this list')->required(),
                Textarea::make('description')
                    ->label('What it is for')
                    ->helperText('A note to yourself and whoever edits this next.')
                    ->rows(2),
            ]),

            Advanced::section(),
        ]);
    }
}

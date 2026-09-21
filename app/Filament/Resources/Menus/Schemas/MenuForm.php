<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Filament\Support\Advanced;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->label('What to call this menu')
                    ->helperText('Only you see this — it does not appear on the website.')
                    ->required(),
            ]),

            Advanced::section(),
        ]);
    }
}

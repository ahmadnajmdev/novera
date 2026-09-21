<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('from')->label('From path')->required()->maxLength(2048)
                    ->helperText('Path only, e.g. /old-projects'),
                TextInput::make('to')->label('To')->required()->maxLength(2048)
                    ->helperText('A path or a full URL.'),
                Select::make('status')->label('Type')->native(false)
                    ->options([301 => 'Permanent (301)', 302 => 'Temporary (302)'])->default(301),
                Toggle::make('is_active')->label('Active')->default(true),
            ])->columns(2),
        ]);
    }
}

<?php

namespace App\Filament\Resources\Locales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Language')
                ->description('Adding a language here adds a tab to every content form on the site.')
                ->schema([
                    TextInput::make('code')->label('Code')->required()->unique(ignoreRecord: true)
                        ->maxLength(12)->helperText('URL prefix and hreflang, e.g. en, ku, ar.'),
                    TextInput::make('name')->label('English name')->required(),
                    TextInput::make('native_name')->label('Shown in the switcher')->required()
                        ->helperText('Written in the language itself, e.g. کوردی.'),
                    Select::make('direction')->label('Direction')->required()->native(false)
                        ->options(['ltr' => 'Left to right', 'rtl' => 'Right to left'])->default('ltr'),
                ])->columns(2),

            Section::make('Typography')->schema([
                TextInput::make('heading_font')->label('Heading font stack'),
                TextInput::make('body_font')->label('Body font stack'),
            ])->columns(2),

            Section::make()->schema([
                TextInput::make('sort')->label('Order')->numeric()->default(0),
                Toggle::make('is_default')->label('Default language')
                    ->helperText('The language content is authored in. Only one can be default.'),
                Toggle::make('is_active')->label('Available on the site')->default(true),
            ])->columns(3),
        ]);
    }
}

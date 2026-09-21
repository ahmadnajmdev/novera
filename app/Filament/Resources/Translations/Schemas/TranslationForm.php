<?php

namespace App\Filament\Resources\Translations\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Source')
                ->description('The English wording exactly as it appears on the site.')
                ->schema([
                    Textarea::make('source')->label('Source text')->required()->rows(2)->columnSpanFull(),
                    TextInput::make('group')->label('Group')->default('site'),
                    Toggle::make('is_pattern')
                        ->label('Regular expression')
                        ->helperText('For generated strings. Use $1 in the translation for each captured group.'),
                ])->columns(2),

            Section::make('Translations')->schema(
                collect(Translatable::locales())
                    ->reject(fn ($locale) => $locale->is_default)
                    ->map(fn ($locale) => Textarea::make("values.{$locale->code}")
                        ->label($locale->name)
                        ->rows(2)
                        ->extraInputAttributes(['dir' => $locale->direction]))
                    ->values()
                    ->all(),
            ),
        ]);
    }
}

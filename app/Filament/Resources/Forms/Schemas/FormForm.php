<?php

namespace App\Filament\Resources\Forms\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("submit_label.{$locale}")
                    ->label('Wording on the send button')
                    ->required($isDefault),
                Textarea::make("success_message.{$locale}")
                    ->label('What the visitor sees after sending')
                    ->rows(2),
            ]),
            Section::make()->schema([
                TextInput::make('name')
                    ->label('What to call this form')
                    ->helperText('Only you see this — it does not appear on the website.')
                    ->required(),
                TextInput::make('notify_email')
                    ->label('Email a copy to')
                    ->helperText('Leave empty and answers only appear under Messages.')
                    ->email(),
                Toggle::make('store_submissions')
                    ->label('Keep answers under Messages')
                    ->default(true),
            ])->columns(2),

            Advanced::section(),
        ]);
    }
}

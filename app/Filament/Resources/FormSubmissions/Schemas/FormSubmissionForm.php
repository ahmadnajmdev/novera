<?php

namespace App\Filament\Resources\FormSubmissions\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Submission')->schema([
                KeyValue::make('payload')->label('Answers')->disabled()->columnSpanFull(),
                TextInput::make('locale')->label('Language')->disabled(),
                TextInput::make('ip')->label('IP address')->disabled(),
                TextInput::make('user_agent')->label('Browser')->disabled()->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}

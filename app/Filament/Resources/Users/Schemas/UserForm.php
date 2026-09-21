<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')->schema([
                TextInput::make('name')->label('Name')->required(),
                TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    // Leaving the field blank on an edit must not wipe the password.
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Leave blank to keep the current password.'),
                Select::make('role')
                    ->label('Role')
                    ->options(UserRole::options())
                    ->default(UserRole::Editor->value)
                    ->required()
                    ->native(false)
                    ->helperText('Editors manage content. Administrators also manage languages, users and settings.'),
                Toggle::make('is_active')->label('Can sign in')->default(true),
            ])->columns(2),
        ]);
    }
}

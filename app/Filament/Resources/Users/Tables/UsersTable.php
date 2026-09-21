<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Models\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('role')->label('Role')->badge()
                    ->formatStateUsing(fn (UserRole $state) => $state->label())
                    ->color(fn (UserRole $state) => $state === UserRole::Admin ? 'warning' : 'gray'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Deleting yourself would lock you out mid-session.
                    DeleteBulkAction::make()->before(fn ($records) => $records->filter(fn (User $u) => $u->is(auth()->user()))->isEmpty()),
                ]),
            ]);
    }
}

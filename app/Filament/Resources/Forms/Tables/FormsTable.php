<?php

namespace App\Filament\Resources\Forms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Form')->searchable(),
                TextColumn::make('key')->label('Reference name')->badge()->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('all_fields_count')->counts('allFields')->label('Questions')->badge(),
                TextColumn::make('submissions_count')->counts('submissions')->label('Answers received')->badge()->color('success'),
                TextColumn::make('notify_email')->label('Emails a copy to')->placeholder('Nobody'),
                IconColumn::make('store_submissions')->label('Kept in Messages')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

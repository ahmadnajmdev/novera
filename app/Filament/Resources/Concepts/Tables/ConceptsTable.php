<?php

namespace App\Filament\Resources\Concepts\Tables;

use App\Filament\Support\Translatable;
use App\Models\Concept;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConceptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('media')
                    ->label('')
                    ->getStateUsing(fn (Concept $record) => nv_img($record->media, 120, 160))
                    ->height(44),
                TextColumn::make('name')
                    ->label('Room type')
                    ->getStateUsing(fn (Concept $record) => nv_tr($record, 'name', Translatable::defaultCode()))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('items_count')->counts('items')->label('Ideas listed')->badge(),
                TextColumn::make('projects_count')->counts('projects')->label('Projects')->badge()->color('gray'),
                IconColumn::make('is_active')->label('Published')->boolean(),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Concept $record) => nv_entity_url($record, Translatable::defaultCode()), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

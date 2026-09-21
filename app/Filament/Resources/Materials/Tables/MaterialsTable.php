<?php

namespace App\Filament\Resources\Materials\Tables;

use App\Filament\Support\Translatable;
use App\Models\Material;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('media')->label('')
                    ->getStateUsing(fn (Material $record) => nv_img($record->media, 120, 160))->height(44),
                TextColumn::make('name')->label('Material')
                    ->getStateUsing(fn (Material $record) => nv_tr($record, 'name', Translatable::defaultCode()))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('group')->label('Group')
                    ->getStateUsing(fn (Material $record) => nv_tr($record->group, 'name', Translatable::defaultCode()))
                    ->badge(),
                TextColumn::make('specs_count')->counts('specs')->label('Details listed')->badge()->color('gray'),
                IconColumn::make('is_active')->label('Published')->boolean(),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Material $record) => nv_entity_url($record, Translatable::defaultCode()), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

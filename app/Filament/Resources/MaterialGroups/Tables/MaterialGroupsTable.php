<?php

namespace App\Filament\Resources\MaterialGroups\Tables;

use App\Filament\Support\Translatable;
use App\Models\MaterialGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Family')
                    ->getStateUsing(fn (MaterialGroup $r) => nv_tr($r, 'name', Translatable::defaultCode())),
                ColorColumn::make('background')->label('Background'),
                ColorColumn::make('foreground')->label('Text'),
                TextColumn::make('materials_count')->counts('materials')->label('Materials')->badge(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

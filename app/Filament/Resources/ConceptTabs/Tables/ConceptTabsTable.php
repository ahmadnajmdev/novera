<?php

namespace App\Filament\Resources\ConceptTabs\Tables;

use App\Filament\Support\Translatable;
use App\Models\ConceptTab;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConceptTabsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                TextColumn::make('label')->label('Tab')
                    ->getStateUsing(fn (ConceptTab $r) => nv_tr($r, 'label', Translatable::defaultCode())),
                TextColumn::make('key')->label('Key')->badge()->color('gray'),
                TextColumn::make('source')->label('Source')->badge(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

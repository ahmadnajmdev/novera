<?php

namespace App\Filament\Resources\Styles\Tables;

use App\Filament\Support\Translatable;
use App\Models\Style;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StylesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                ImageColumn::make('media')->label('')
                    ->getStateUsing(fn (Style $r) => nv_img($r->media, 120, 160))->height(40),
                TextColumn::make('name')->label('Style')
                    ->getStateUsing(fn (Style $r) => nv_tr($r, 'name', Translatable::defaultCode())),
                IconColumn::make('is_active')->label('Published')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Support\Translatable;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')->defaultSort('sort')
            ->columns([
                TextColumn::make('number')->label('#')->badge(),
                ImageColumn::make('media')->label('')
                    ->getStateUsing(fn (Service $r) => nv_img($r->media, 160, 120))->height(40),
                TextColumn::make('name')->label('Service')
                    ->getStateUsing(fn (Service $r) => nv_tr($r, 'name', Translatable::defaultCode())),
                TextColumn::make('points_count')->counts('points')->label('Bullet points')->badge()->color('gray'),
                IconColumn::make('is_active')->label('Published')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

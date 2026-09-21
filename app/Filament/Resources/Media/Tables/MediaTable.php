<?php

namespace App\Filament\Resources\Media\Tables;

use App\Models\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('folder')
            ->columns([
                ImageColumn::make('preview')->label('')
                    ->getStateUsing(fn (Media $r) => nv_img($r, 160, 160))->height(48),
                TextColumn::make('filename')->label('Name')->searchable()->sortable(),
                TextColumn::make('folder')->label('Folder')->badge()->sortable(),
                TextColumn::make('source')->label('Source')
                    ->getStateUsing(fn (Media $r) => filled($r->external_url) ? 'Remote' : 'Uploaded')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Remote' ? 'gray' : 'success'),
                TextColumn::make('size')->label('Size')
                    ->formatStateUsing(fn (?int $state) => $state ? number_format($state / 1024, 0).' KB' : '—'),
            ])
            ->filters([
                SelectFilter::make('folder')->options(fn () => Media::query()->whereNotNull('folder')->distinct()->pluck('folder', 'folder')),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

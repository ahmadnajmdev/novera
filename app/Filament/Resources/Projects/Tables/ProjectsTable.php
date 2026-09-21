<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Filament\Support\Translatable;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('media')->label('')
                    ->getStateUsing(fn (Project $record) => nv_img($record->media, 160, 120))->height(44),
                TextColumn::make('name')->label('Project')
                    ->getStateUsing(fn (Project $record) => nv_tr($record, 'name', Translatable::defaultCode()))
                    ->description(fn (Project $record) => nv_tr($record, 'location', Translatable::defaultCode()))
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'like', "%{$search}%")),
                TextColumn::make('category.name')->label('Category')
                    ->getStateUsing(fn (Project $record) => nv_tr($record->category, 'name', Translatable::defaultCode()))->badge(),
                TextColumn::make('status.name')->label('Stage')
                    ->getStateUsing(fn (Project $record) => nv_tr($record->status, 'name', Translatable::defaultCode()))->badge()->color('gray'),
                TextColumn::make('year')->label('Year'),
                IconColumn::make('is_featured')->label('On home page')->boolean(),
                IconColumn::make('is_active')->label('Published')->boolean(),
            ])
            ->filters([
                SelectFilter::make('project_category_id')->label('Category')
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => nv_tr($record, 'name', Translatable::defaultCode())),
                SelectFilter::make('project_status_id')->label('Stage')
                    ->relationship('status', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => nv_tr($record, 'name', Translatable::defaultCode())),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Project $record) => nv_entity_url($record, Translatable::defaultCode()), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

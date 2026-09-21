<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Filament\Support\Translatable;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Page')
                    ->formatStateUsing(fn ($state, Page $record) => nv_tr($record, 'title', Translatable::defaultCode()))
                    ->searchable(query: fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
                    ->sortable(),
                // Jargon: available for anyone who needs it, hidden by default.
                TextColumn::make('key')->label('Reference name')->badge()->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slug')
                    ->label('Web address')
                    // getStateUsing, not formatStateUsing: the home page's slug
                    // is empty, and a blank state never reaches the formatter.
                    ->getStateUsing(fn (Page $record) => '/'.Translatable::defaultCode().'/'
                        .$record->getTranslation('slug', Translatable::defaultCode()))
                    ->color('gray'),
                TextColumn::make('sections_count')->counts('sections')->label('Sections')->badge(),
                IconColumn::make('published_at')
                    ->label('Published')
                    ->boolean()
                    ->tooltip(fn (Page $record) => $record->isPublished()
                        ? 'Everyone can see this page'
                        : 'Draft — only signed-in editors can see it')
                    ->getStateUsing(fn (Page $record) => $record->isPublished()),
                TextColumn::make('sort')->label('Position')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort')
            ->recordActions([
                Action::make('edit_visually')
                    ->label('Edit visually')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->color('primary')
                    ->button()
                    ->tooltip('Click text on the page and type over it')
                    ->url(fn (Page $record) => \App\Filament\Pages\VisualEditor::getUrl(['page' => $record->getKey()])),
                Action::make('view')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $record) => nv_url($record, Translatable::defaultCode()), shouldOpenInNewTab: true),
                EditAction::make()->label('Settings'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}

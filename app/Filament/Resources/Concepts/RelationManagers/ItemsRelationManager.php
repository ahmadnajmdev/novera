<?php

namespace App\Filament\Resources\Concepts\RelationManagers;

use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use App\Models\ConceptItem;
use App\Models\ConceptTab;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Tab items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tab')
                ->label('Tab')
                ->options(fn () => ConceptTab::where('source', 'items')->pluck('key', 'key'))
                ->required()
                ->native(false),
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("title.{$locale}")->label('Title')->required($isDefault),
            ]),
            MediaPicker::make('media_id', 'Image'),
            TextInput::make('sort')->label('Order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tab')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->groups(['tab'])
            ->columns([
                ImageColumn::make('media')
                    ->label('')
                    ->getStateUsing(fn (ConceptItem $record) => nv_img($record->media, 120, 150))
                    ->height(40),
                TextColumn::make('tab')->badge(),
                TextColumn::make('title')
                    ->label('Title')
                    ->getStateUsing(fn (ConceptItem $record) => nv_tr($record, 'title', Translatable::defaultCode())),
            ])
            ->headerActions([CreateAction::make()->label('Add item')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

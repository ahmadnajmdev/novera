<?php

namespace App\Filament\Resources\ContentCollections\RelationManagers;

use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use App\Models\ContentItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Rows in a generic list — stats, standards, facilities, contact details,
 * social links, filter chips. Label and value are translatable; anything a
 * particular list needs beyond that goes in "extra".
 */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Rows';

    protected static ?string $modelLabel = 'row';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("label.{$locale}")->label('Label')->required($isDefault),
                Textarea::make("value.{$locale}")->label('Value')->rows(2),
            ]),
            Section::make('Options')->schema([
                TextInput::make('url')
                    ->label('Link (optional)')
                    ->helperText('Leave empty unless this row should be clickable.')
                    ->maxLength(2048),
                MediaPicker::make('media_id', 'Picture (optional)'),
                Toggle::make('is_active')->label('Show this row')->default(true),
            ])->columns(2),

            Section::make('Advanced')
                ->description('Only a few lists use these. Leave them alone unless you know you need them.')
                ->collapsed()
                ->schema([
                    TextInput::make('key')
                        ->label('Reference name')
                        ->helperText('Only needed when a page points at one specific row.'),
                    TextInput::make('sort')
                        ->label('Position in the list')
                        ->helperText('Lower numbers come first. Dragging the rows sets this for you.')
                        ->numeric()
                        ->default(0),
                    KeyValue::make('extra')
                        ->label('Extra details')
                        ->keyLabel('Name')
                        ->valueLabel('Value')
                        ->helperText('Used by a couple of lists only — "hex" for a colour swatch, '
                            .'"type" and "match" for the portfolio filter buttons.')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->description('Drag a row by its handle to change the order they appear in.')
            ->columns([
                TextColumn::make('label')->label('Label')
                    ->getStateUsing(fn (ContentItem $r) => nv_tr($r, 'label', Translatable::defaultCode())),
                TextColumn::make('value')->label('Value')
                    ->getStateUsing(fn (ContentItem $r) => nv_tr($r, 'value', Translatable::defaultCode()))
                    ->limit(60)->wrap(),
                TextColumn::make('url')->label('Link')->limit(30)->placeholder('—')->color('gray'),
                IconColumn::make('is_active')->label('Shown')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Add a row')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

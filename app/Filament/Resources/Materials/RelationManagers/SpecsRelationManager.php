<?php

namespace App\Filament\Resources\Materials\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\MaterialSpec;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecsRelationManager extends RelationManager
{
    protected static string $relationship = 'specs';

    protected static ?string $title = 'Specification rows';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("label.{$locale}")->label('Label')->required($isDefault),
                TextInput::make("value.{$locale}")->label('Value')->required($isDefault),
            ]),
            TextInput::make('sort')->label('Order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('label')->label('Label')
                    ->getStateUsing(fn (MaterialSpec $r) => nv_tr($r, 'label', Translatable::defaultCode())),
                TextColumn::make('value')->label('Value')
                    ->getStateUsing(fn (MaterialSpec $r) => nv_tr($r, 'value', Translatable::defaultCode())),
            ])
            ->headerActions([CreateAction::make()->label('Add row')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}

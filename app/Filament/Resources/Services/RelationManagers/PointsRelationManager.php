<?php

namespace App\Filament\Resources\Services\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\ServicePoint;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointsRelationManager extends RelationManager
{
    protected static string $relationship = 'points';

    protected static ?string $title = 'Bullet points';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("text.{$locale}")->label('Text')->required($isDefault),
            ]),
            TextInput::make('sort')->label('Order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')->reorderable('sort')->defaultSort('sort')
            ->columns([
                TextColumn::make('text')->label('Text')
                    ->getStateUsing(fn (ServicePoint $r) => nv_tr($r, 'text', Translatable::defaultCode())),
            ])
            ->headerActions([CreateAction::make()->label('Add point')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}

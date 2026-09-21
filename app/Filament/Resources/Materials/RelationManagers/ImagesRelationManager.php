<?php

namespace App\Filament\Resources\Materials\RelationManagers;

use App\Filament\Support\MediaPicker;
use App\Models\MaterialImage;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Gallery';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            MediaPicker::make('media_id', 'Image')->required(),
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
                ImageColumn::make('media')->label('')
                    ->getStateUsing(fn (MaterialImage $r) => nv_img($r->media, 160, 160))->height(56),
                TextColumn::make('media.filename')->label('File'),
            ])
            ->headerActions([CreateAction::make()->label('Add image')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}

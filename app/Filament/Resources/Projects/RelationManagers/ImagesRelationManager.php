<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Support\MediaPicker;
use App\Models\ProjectImage;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
            Select::make('ratio')->label('Aspect ratio')
                ->options(['3/4' => 'Portrait 3:4', '4/5' => 'Portrait 4:5', '1/1' => 'Square', '16/9' => 'Wide 16:9'])
                ->default('3/4')->native(false),
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
                    ->getStateUsing(fn (ProjectImage $r) => nv_img($r->media, 160, 160))->height(56),
                TextColumn::make('media.filename')->label('File'),
                TextColumn::make('ratio')->label('Ratio')->badge(),
            ])
            ->headerActions([CreateAction::make()->label('Add image')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}

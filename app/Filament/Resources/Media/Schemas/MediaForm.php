<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('File')
                ->description('Upload a file, or point at a remote URL for imagery hosted elsewhere.')
                ->schema([
                    FileUpload::make('path')
                        ->label('Upload')
                        ->disk('public')
                        ->directory('uploads')
                        ->image()
                        ->imageEditor()
                        ->maxSize(20480)
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $set('filename', $state->getClientOriginalName());
                                $set('mime_type', $state->getMimeType());
                                $set('size', $state->getSize());
                            }
                        }),
                    TextInput::make('external_url')->label('Remote URL')->url()->maxLength(2048),
                    TextInput::make('filename')->label('Name'),
                    TextInput::make('folder')->label('Folder')->datalist(['brand', 'placeholders', 'projects', 'materials', 'concepts', 'uploads']),
                ])->columns(2),

            Translatable::tabs(fn (string $locale) => [
                TextInput::make("alt.{$locale}")->label('Alt text')->helperText('Describes the image for screen readers and search engines.'),
                TextInput::make("caption.{$locale}")->label('Caption'),
            ], 'Descriptions'),
        ]);
    }
}

<?php

namespace App\Filament\Support;

use App\Models\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

/**
 * A single picker for the whole media library, covering both uploaded files
 * and the remote placeholders the design shipped with.
 *
 * Options carry a thumbnail, because "which picture is novera-03.jpg?" is not
 * a question anyone should have to answer from memory, and the picker can
 * upload a new file inline so choosing a photo never means abandoning a
 * half-filled form to go to another screen.
 */
class MediaPicker
{
    public static function make(string $name, string $label = 'Photo'): Select
    {
        return Select::make($name)
            ->label($label)
            ->options(fn () => static::options())
            ->allowHtml()
            ->searchable()
            ->preload()
            ->native(false)
            ->placeholder('No picture')
            ->helperText('Not listed? Use + to upload one.')
            ->createOptionForm([
                FileUpload::make('path')
                    ->label('Choose a file')
                    ->disk('public')
                    ->directory('uploads')
                    ->image()
                    ->imageEditor()
                    ->maxSize(20480)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('alt')
                    ->label('Describe the picture')
                    ->helperText('One short line. Read aloud to visitors who cannot see it.')
                    ->columnSpanFull(),
            ])
            ->createOptionUsing(function (array $data): int {
                $path = $data['path'];

                $media = Media::create([
                    'filename' => basename($path),
                    'path' => $path,
                    'disk' => 'public',
                    'folder' => 'uploads',
                    'alt' => filled($data['alt'] ?? null)
                        ? [Translatable::defaultCode() => $data['alt']]
                        : null,
                ]);

                return $media->id;
            })
            ->createOptionModalHeading('Upload a picture');
    }

    /** @return array<int, string> */
    protected static function options(): array
    {
        return Media::query()
            ->orderBy('folder')
            ->orderBy('filename')
            ->get()
            ->mapWithKeys(fn (Media $media) => [$media->id => static::optionLabel($media)])
            ->all();
    }

    protected static function optionLabel(Media $media): string
    {
        $thumbnail = e(nv_img($media, 80, 80));
        $name = e(Str::of($media->filename)->beforeLast('.')->replace(['-', '_'], ' ')->title());
        $folder = e($media->folder ?: 'Library');

        return <<<HTML
            <span style="display:flex;align-items:center;gap:.625rem;">
                <img src="{$thumbnail}" alt="" loading="lazy"
                     style="width:2.25rem;height:2.25rem;border-radius:.375rem;object-fit:cover;flex-shrink:0;background:rgba(0,0,0,.06);">
                <span style="display:flex;flex-direction:column;line-height:1.25;min-width:0;">
                    <span style="font-weight:500;">{$name}</span>
                    <span style="font-size:.75rem;opacity:.6;">{$folder}</span>
                </span>
            </span>
            HTML;
    }
}

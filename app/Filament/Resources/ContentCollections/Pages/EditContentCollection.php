<?php

namespace App\Filament\Resources\ContentCollections\Pages;

use App\Filament\Resources\ContentCollections\ContentCollectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentCollection extends EditRecord
{
    protected static string $resource = ContentCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

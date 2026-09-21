<?php

namespace App\Filament\Resources\MaterialGroups\Pages;

use App\Filament\Resources\MaterialGroups\MaterialGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaterialGroup extends EditRecord
{
    protected static string $resource = MaterialGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

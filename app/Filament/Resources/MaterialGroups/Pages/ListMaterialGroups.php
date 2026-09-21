<?php

namespace App\Filament\Resources\MaterialGroups\Pages;

use App\Filament\Resources\MaterialGroups\MaterialGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterialGroups extends ListRecords
{
    protected static string $resource = MaterialGroupResource::class;

    public function getSubheading(): ?string
    {
        return 'How materials are grouped on the materials page.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

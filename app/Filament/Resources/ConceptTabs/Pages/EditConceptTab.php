<?php

namespace App\Filament\Resources\ConceptTabs\Pages;

use App\Filament\Resources\ConceptTabs\ConceptTabResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConceptTab extends EditRecord
{
    protected static string $resource = ConceptTabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

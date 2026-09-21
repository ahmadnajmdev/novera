<?php

namespace App\Filament\Resources\ConceptTabs\Pages;

use App\Filament\Resources\ConceptTabs\ConceptTabResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConceptTabs extends ListRecords
{
    protected static string $resource = ConceptTabResource::class;

    public function getSubheading(): ?string
    {
        return 'The tabs shown across a room-type page.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ContentCollections\Pages;

use App\Filament\Resources\ContentCollections\ContentCollectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentCollections extends ListRecords
{
    protected static string $resource = ContentCollectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Small repeating lists the pages read from — figures, standards, contact rows, social links. Open one to edit its rows.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

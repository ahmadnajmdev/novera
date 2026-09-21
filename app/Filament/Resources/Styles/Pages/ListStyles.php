<?php

namespace App\Filament\Resources\Styles\Pages;

use App\Filament\Resources\Styles\StyleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStyles extends ListRecords
{
    protected static string $resource = StyleResource::class;

    public function getSubheading(): ?string
    {
        return 'Design directions shown on the room-type pages.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;

    public function getSubheading(): ?string
    {
        return 'Everything sent through a form on the website. Nothing here is published — it is your inbox.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

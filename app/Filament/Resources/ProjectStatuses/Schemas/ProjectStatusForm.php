<?php

namespace App\Filament\Resources\ProjectStatuses\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")->label('Status name')->required($isDefault),
            ]),
            Advanced::section([Advanced::position()]),
        ]);
    }
}

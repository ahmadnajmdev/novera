<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")->label('Category name')->required($isDefault),
            ]),
            Advanced::section([Advanced::position()]),
        ]);
    }
}

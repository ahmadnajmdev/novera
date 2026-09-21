<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Advanced;
use App\Filament\Support\MediaPicker;
use App\Filament\Support\Translatable;
use App\Models\Concept;
use App\Models\Material;
use App\Models\ProjectCategory;
use App\Models\ProjectStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                TextInput::make("name.{$locale}")
                    ->label('Project name')
                    ->required($isDefault)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get) use ($locale) {
                        if (blank($get("slug.{$locale}")) && filled($state)) {
                            $set("slug.{$locale}", Str::slug($state));
                        }
                    }),
                TextInput::make("slug.{$locale}")
                    ->label('Web address')
                    ->helperText('Filled in from the name.')
                    ->rule('regex:/^[a-z0-9\-]*$/'),
                TextInput::make("location.{$locale}")->label('Where it is'),
                TextInput::make("headline.{$locale}")->label('Headline'),
                Textarea::make("body.{$locale}")->label('Description')->rows(5),
                TextInput::make("seo_title.{$locale}")->label('Title in Google results'),
                Textarea::make("seo_description.{$locale}")->label('Description in Google results')->rows(2),
            ]),

            Section::make('Details')->schema([
                TextInput::make('year')->label('Year completed')->maxLength(10),
                Select::make('project_category_id')->label('Category')
                    ->options(fn () => ProjectCategory::all()->mapWithKeys(fn ($c) => [$c->id => nv_tr($c, 'name', Translatable::defaultCode())]))
                    ->native(false),
                Select::make('project_status_id')->label('Stage')
                    ->options(fn () => ProjectStatus::all()->mapWithKeys(fn ($c) => [$c->id => nv_tr($c, 'name', Translatable::defaultCode())]))
                    ->native(false),
                MediaPicker::make('media_id', 'Main photo'),
                MediaPicker::make('wide_media_id', 'Wide photo'),
                Toggle::make('is_featured')->label('Highlight on the home page'),
                Toggle::make('is_active')
                    ->label('Published — visible on the website')
                    ->default(true),
            ])->columns(2),

            Section::make('What went into it')
                ->description('Optional. Used to cross-link this project from the material and room pages.')
                ->schema([
                Select::make('materials')
                    ->label('Materials used')
                    ->relationship('materials')
                    ->getOptionLabelFromRecordUsing(fn (Material $record) => nv_tr($record, 'name', Translatable::defaultCode()))
                    ->multiple()->preload()->native(false),
                Select::make('concepts')
                    ->label('Room types delivered')
                    ->relationship('concepts')
                    ->getOptionLabelFromRecordUsing(fn (Concept $record) => nv_tr($record, 'name', Translatable::defaultCode()))
                    ->multiple()->preload()->native(false),
            ])->columns(2),

            Advanced::section([Advanced::position()]),
        ]);
    }
}

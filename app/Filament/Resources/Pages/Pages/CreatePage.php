<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Forms\CardPicker;
use App\Filament\Pages\VisualEditor;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\PageLayouts;
use App\Filament\Support\Translatable;
use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Str;

/**
 * Creating a page in three questions.
 *
 * The plain resource form produced a page that was empty, unpublished and
 * absent from every menu — three more screens to visit before anything was
 * visible, none of them signposted. Each of those is now a step here, and
 * the page you land on already has content to rewrite.
 */
class CreatePage extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = PageResource::class;

    public function getTitle(): string
    {
        return 'Add a page';
    }

    public function getSteps(): array
    {
        $locale = Translatable::defaultCode();

        return [
            Step::make('What is it called?')
                ->icon('heroicon-o-pencil')
                ->description('The name people see')
                ->schema([
                    TextInput::make("title.{$locale}")
                        ->label('Page name')
                        ->required()
                        ->autofocus()
                        ->live(onBlur: true)
                        ->helperText('You can add the other languages once the page exists.')
                        ->afterStateUpdated(function ($state, $set, $get) use ($locale) {
                            if (blank($get("slug.{$locale}")) && filled($state)) {
                                $set("slug.{$locale}", Str::slug($state));
                            }
                        }),
                    TextInput::make("slug.{$locale}")
                        ->label('Web address')
                        ->prefix(rtrim(config('app.url'), '/').'/'.$locale.'/')
                        ->helperText('Filled in from the name. Change it only if you want a different link.')
                        ->rule('regex:/^[a-z0-9\-\/]*$/')
                        ->validationMessages([
                            'regex' => 'Use lower-case letters, numbers and hyphens only — no spaces.',
                        ]),
                ]),

            Step::make('What goes on it?')
                ->icon('heroicon-o-rectangle-group')
                ->description('A starting arrangement')
                ->schema([
                    CardPicker::make('starting_layout')
                        ->label('Start this page as')
                        ->hiddenLabel()
                        ->dehydrated(false)
                        ->default(PageLayouts::default())
                        ->cardColumns(2)
                        ->cards(collect(PageLayouts::all())
                            ->map(fn (array $layout) => [
                                'label' => $layout['label'],
                                'hint' => $layout['hint'],
                                'icon' => $layout['icon'],
                            ])->all()),
                ]),

            Step::make('Who can find it?')
                ->icon('heroicon-o-eye')
                ->description('Menus and publishing')
                ->schema([
                    CheckboxList::make('add_to_menus')
                        ->label('Add a link to this page in')
                        ->dehydrated(false)
                        ->options(fn () => Menu::orderBy('id')->pluck('name', 'id'))
                        ->helperText('You can always change the menus later.')
                        ->columns(2),
                    Toggle::make('publish_now')
                        ->label('Publish it straight away')
                        ->helperText('Off keeps it a draft only you can see, so you can finish it first.')
                        ->dehydrated(false)
                        ->default(true),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['published_at'] = ($this->data['publish_now'] ?? true) ? now() : null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $locale = Translatable::defaultCode();
        $layout = $this->data['starting_layout'] ?? PageLayouts::default();

        PageLayouts::apply($this->record, $layout, $locale);

        foreach ((array) ($this->data['add_to_menus'] ?? []) as $menuId) {
            MenuItem::create([
                'menu_id' => $menuId,
                'page_id' => $this->record->id,
                'label' => [$locale => nv_tr($this->record, 'title', $locale)],
                'sort' => (int) MenuItem::where('menu_id', $menuId)->max('sort') + 1,
                'is_visible' => true,
            ]);
        }

        $count = count(PageLayouts::sections($layout));

        Notification::make()
            ->success()
            ->title('Page created')
            ->body($count > 0
                ? "It has {$count} sections filled with placeholder wording. Click any of it to rewrite it."
                : 'It has no sections yet — add the first one from the page’s own screen.')
            ->send();
    }

    /** Straight to the place where the words get written. */
    protected function getRedirectUrl(): string
    {
        $layout = $this->data['starting_layout'] ?? PageLayouts::default();

        return PageLayouts::sections($layout) === []
            ? PageResource::getUrl('edit', ['record' => $this->record])
            : VisualEditor::getUrl(['page' => $this->record->getKey()]);
    }
}

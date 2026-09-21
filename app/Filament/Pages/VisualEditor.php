<?php

namespace App\Filament\Pages;

use App\Models\Locale;
use App\Models\Media;
use App\Models\Page as ContentPage;
use App\Models\Section;
use App\Support\InlineEditor;
use App\Support\Urls;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Livewire\Attributes\Url;

/**
 * Edit page copy on top of the real rendered site.
 *
 * The preview runs in a same-origin iframe in edit mode. Clicking any marked
 * node sends its identity here; this component loads the stored value, saves
 * the change and pushes the re-rendered fragment back into the frame.
 */
class VisualEditor extends Page
{
    protected string $view = 'filament.pages.visual-editor';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCursorArrowRays;

    protected static ?string $navigationLabel = 'Visual editor';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Edit the site visually';

    public function getSubheading(): ?string
    {
        return 'Click any wording on the page and type over it. Click a picture to swap it. '
            .'Everything saves as you go — there is no publish step to remember.';
    }

    #[Url(as: 'page')]
    public ?int $pageId = null;

    #[Url(as: 'locale')]
    public string $locale = '';

    public string $device = 'desktop';

    /** Currently selected node, as reported by the preview frame. */
    public ?array $selection = null;

    /** Picture slot awaiting a choice, as reported by the preview frame. */
    public ?array $mediaSelection = null;

    public ?string $value = null;

    public bool $dirty = false;

    /** What was last saved, so the panel can say so without a page reload. */
    public ?string $lastSaved = null;

    public function mount(): void
    {
        $this->locale = $this->locale ?: (Locale::where('is_default', true)->value('code') ?? 'en');
        $this->pageId ??= ContentPage::where('key', 'home')->value('id') ?? ContentPage::value('id');
    }

    public function getPages(): Collection
    {
        return ContentPage::orderBy('sort')->get();
    }

    public function getLocales(): Collection
    {
        return Locale::active()->get();
    }

    public function getPreviewUrl(): string
    {
        $page = ContentPage::find($this->pageId);

        if (! $page) {
            return url('/'.$this->locale);
        }

        $base = app(Urls::class)->page($page, $this->locale);

        return $base.(str_contains($base, '?') ? '&' : '?').config('novera.edit_param').'=1';
    }

    public function updatedPageId(): void
    {
        $this->clearSelection();
        $this->dispatch('nv-reload-preview', url: $this->getPreviewUrl());
    }

    public function updatedLocale(): void
    {
        $this->clearSelection();
        $this->dispatch('nv-reload-preview', url: $this->getPreviewUrl());
    }

    /** Called from the preview frame when an editable node is clicked. */
    public function selectNode(array $payload): void
    {
        $editor = app(InlineEditor::class);

        try {
            $stored = $editor->read(
                $payload['model'],
                $payload['id'],
                $payload['field'],
                $payload['locale'] ?: $this->locale,
            );
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title('That element is not editable')->body($exception->getMessage())->send();

            return;
        }

        $this->mediaSelection = null;

        $this->selection = [
            'model' => $payload['model'],
            'id' => $payload['id'],
            'field' => $payload['field'],
            'locale' => $payload['locale'] ?: $this->locale,
            'label' => $payload['label'] ?? null,
        ];

        // Nothing stored for this locale yet: start from the source language so
        // the editor is translating rather than typing from scratch.
        $this->value = $stored ?? $editor->read(
            $payload['model'],
            $payload['id'],
            $payload['field'],
            config('app.fallback_locale'),
        );

        $this->dirty = false;
    }

    public function save(): void
    {
        if (! $this->selection) {
            return;
        }

        $editor = app(InlineEditor::class);

        try {
            $editor->write(
                $this->selection['model'],
                $this->selection['id'],
                $this->selection['field'],
                $this->selection['locale'],
                $this->value,
            );
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title('Could not save')->body($exception->getMessage())->send();

            return;
        }

        $this->dirty = false;

        $this->dispatch('nv-apply', payload: [
            'model' => $this->selection['model'],
            'id' => $this->selection['id'],
            'field' => $this->selection['field'],
            'html' => $editor->preview($this->selection['field'], $this->value, $this->selection['locale']),
        ]);

        Notification::make()->success()->title('Saved')->send();
    }

    /**
     * Text typed straight into the page. The frame owns the wording, so this
     * only records it — nothing is echoed back, which would fight the caret.
     */
    public function saveInlineEdit(array $payload): void
    {
        $editor = app(InlineEditor::class);
        $locale = $payload['locale'] ?: $this->locale;

        try {
            $editor->write(
                $payload['model'],
                $payload['id'],
                $payload['field'],
                $locale,
                $payload['value'] ?? '',
            );
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title('Could not save')->body($exception->getMessage())->send();

            return;
        }

        $this->value = $payload['value'] ?? '';
        $this->dirty = false;
        $this->lastSaved = now()->format('H:i');

        Notification::make()->success()->title('Saved')->send();
    }

    public function updatedValue(): void
    {
        $this->dirty = true;
    }

    public function clearSelection(): void
    {
        $this->selection = null;
        $this->value = null;
        $this->dirty = false;
        $this->lastSaved = null;
        $this->dispatch('nv-deselect');
    }

    /** The current page's sections, for the panel's outline. */
    public function getSections(): Collection
    {
        $page = ContentPage::find($this->pageId);

        return $page ? $page->sections()->orderBy('sort')->get() : collect();
    }

    public function toggleSection(int $id): void
    {
        $section = $this->sectionOnCurrentPage($id);

        if (! $section) {
            return;
        }

        $section->update(['is_visible' => ! $section->is_visible]);

        $this->dispatch('nv-reload-preview', url: $this->getPreviewUrl());
    }

    /**
     * Move a section one place up or down. Sorts are rewritten from the
     * current order rather than swapped, so a list that drifted out of step
     * (duplicated sorts, gaps) comes back consistent.
     */
    public function moveSection(int $id, string $direction): void
    {
        $sections = $this->getSections()->values();
        $index = $sections->search(fn (Section $section) => $section->id === $id);

        if ($index === false) {
            return;
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $sections->count()) {
            return;
        }

        $ordered = $sections->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        foreach ($ordered as $position => $section) {
            $section->update(['sort' => $position]);
        }

        $this->dispatch('nv-reload-preview', url: $this->getPreviewUrl());
    }

    public function scrollToSection(int $id): void
    {
        $this->dispatch('nv-scroll-to-section', id: $id);
    }

    protected function sectionOnCurrentPage(int $id): ?Section
    {
        return Section::where('id', $id)->where('page_id', $this->pageId)->first();
    }

    /** Called from the preview frame when a picture is clicked. */
    public function pickMedia(array $payload): void
    {
        $editor = app(InlineEditor::class);

        try {
            $current = $editor->readMedia($payload['model'], $payload['id'], $payload['field']);
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title('That picture is not editable')->body($exception->getMessage())->send();

            return;
        }

        $this->clearSelection();

        $this->mediaSelection = [
            'model' => $payload['model'],
            'id' => $payload['id'],
            'field' => $payload['field'],
            'current' => $current,
        ];
    }

    public function chooseMedia(int $mediaId): void
    {
        if (! $this->mediaSelection) {
            return;
        }

        $editor = app(InlineEditor::class);

        try {
            $editor->writeMedia(
                $this->mediaSelection['model'],
                $this->mediaSelection['id'],
                $this->mediaSelection['field'],
                $mediaId,
            );
        } catch (InvalidArgumentException $exception) {
            Notification::make()->danger()->title('Could not change the picture')->body($exception->getMessage())->send();

            return;
        }

        $this->mediaSelection['current'] = $mediaId;

        $this->dispatch('nv-apply-media', payload: [
            'model' => $this->mediaSelection['model'],
            'id' => $this->mediaSelection['id'],
            'field' => $this->mediaSelection['field'],
            'url' => nv_img(Media::find($mediaId), 2200, 1200),
        ]);

        Notification::make()->success()->title('Picture changed')->send();
    }

    /** @return Collection<int, Media> */
    public function getMediaLibrary(): Collection
    {
        return Media::query()->orderBy('folder')->orderBy('filename')->get();
    }

    public function clearMediaSelection(): void
    {
        $this->mediaSelection = null;
    }

    public function getFieldLabel(): string
    {
        return str($this->selection['field'] ?? '')->replace('_', ' ')->title()->toString();
    }

    public function isHeading(): bool
    {
        return ($this->selection['field'] ?? null) === 'heading';
    }
}

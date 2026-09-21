<?php

namespace App\Livewire;

use App\Models\Concept;
use App\Models\ConceptTab;
use App\Models\Style;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Concept detail tabs. Tab definitions live in the CMS, including which source
 * feeds each one, so a new tab needs no template change.
 */
class ConceptTabs extends Component
{
    public Concept $concept;

    #[Url(as: 'tab', except: '')]
    public string $tab = '';

    protected function tabs(): Collection
    {
        return ConceptTab::visible()->get();
    }

    public function select(string $key): void
    {
        $this->tab = $key;
    }

    public function render()
    {
        $tabs = $this->tabs();
        $active = $tabs->firstWhere('key', $this->tab) ?? $tabs->first();

        $items = $active?->source === 'styles'
            ? Style::active()->with('media')->get()->map(fn (Style $style) => [
                'title' => nv_tr($style, 'name'),
                'media' => $style->media,
            ])
            : $this->concept->itemsFor($active?->key ?? 'ideas')->with('media')->get()->map(fn ($item) => [
                'title' => nv_tr($item, 'title'),
                'media' => $item->media,
            ]);

        // "Ideas we return to for the kitchen" — the concept name is folded in
        // lowercase, matching how the dictionary keys its pattern entries.
        $heading = str_replace(
            ':concept',
            mb_strtolower(nv_tr($this->concept, 'name', config('app.fallback_locale'))),
            nv_tr($active, 'heading_template', config('app.fallback_locale')),
        );

        return view('livewire.concept-tabs', [
            'tabs' => $tabs,
            'active' => $active,
            'items' => $items->values(),
            'heading' => nv_t($heading),
        ]);
    }
}

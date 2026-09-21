<?php

namespace App\Livewire;

use App\Models\Material;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * The home page material list: hovering a name swaps the preview image.
 * Rendered server-side so the list is crawlable, then made interactive.
 */
class MaterialIndex extends Component
{
    public string $ctaLabel = '';

    /** So the visual editor can mark the button wording as editable. */
    public ?int $sectionId = null;

    public string $ctaPage = 'materials';

    public ?int $activeId = null;

    public function mount(): void
    {
        $this->activeId ??= $this->materials()->get(8)?->id ?? $this->materials()->first()?->id;
    }

    public function highlight(int $id): void
    {
        $this->activeId = $id;
    }

    protected function materials(): Collection
    {
        return Material::active()->with('media')->get();
    }

    public function render()
    {
        $materials = $this->materials();

        return view('livewire.material-index', [
            'materials' => $materials,
            'active' => $materials->firstWhere('id', $this->activeId) ?? $materials->first(),
        ]);
    }
}

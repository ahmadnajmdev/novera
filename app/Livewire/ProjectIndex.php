<?php

namespace App\Livewire;

use App\Models\Project;
use App\Support\Site;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Filterable project grid. Filters are CMS content, so adding one is a row in
 * the "project_filters" collection rather than a code change.
 */
class ProjectIndex extends Component
{
    public bool $showFilters = true;

    #[Url(as: 'filter', except: '')]
    public string $filter = '';

    public function select(string $key): void
    {
        $this->filter = $key;
    }

    protected function filters(): Collection
    {
        return app(Site::class)->collection('project_filters');
    }

    protected function activeFilter(): ?object
    {
        $filters = $this->filters();

        return $filters->first(fn ($item) => $this->keyFor($item) === $this->filter) ?? $filters->first();
    }

    protected function keyFor(object $item): string
    {
        $type = data_get($item->extra, 'type');
        $match = data_get($item->extra, 'match');

        return $type === 'all' ? '' : $type.':'.$match;
    }

    public function render()
    {
        $active = $this->activeFilter();
        $type = data_get($active?->extra, 'type');
        $match = data_get($active?->extra, 'match');

        $query = Project::active()->with('media', 'category', 'status');

        match ($type) {
            'status' => $query->whereRelation('status', 'key', $match),
            'category' => $query->whereRelation('category', 'key', $match),
            'concept' => $query->whereHas('concepts', fn ($q) => $q->where('key', $match)),
            default => null,
        };

        return view('livewire.project-index', [
            'projects' => $query->get(),
            'filters' => $this->filters(),
            'activeKey' => $this->keyFor($active ?? (object) ['extra' => ['type' => 'all']]),
            'keyFor' => fn (object $item) => $this->keyFor($item),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Concept;
use App\Models\ConceptItem;
use App\Models\ConceptTab;
use App\Models\Material;
use App\Models\MaterialGroup;
use App\Models\MaterialImage;
use App\Models\MaterialSpec;
use App\Models\Media;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Models\ProjectStatus;
use App\Models\Service;
use App\Models\ServicePoint;
use App\Models\Style;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    protected array $data;

    /** Placeholder key ("kitchenBar") => media id. */
    protected array $mediaByKey = [];

    public function run(): void
    {
        $this->data = json_decode(
            file_get_contents(database_path('seeders/data/design.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->mediaByKey = Media::where('folder', 'placeholders')
            ->pluck('id', 'filename')
            ->all();

        $this->seedConceptTabs();
        $this->seedConcepts();
        $this->seedStyles();
        $this->seedMaterials();
        $this->seedProjects();
        $this->seedServices();
        $this->linkRelations();
    }

    protected function media(?string $key): ?int
    {
        return $key ? ($this->mediaByKey[$key] ?? null) : null;
    }

    protected function seedConceptTabs(): void
    {
        $tabs = [
            ['key' => 'ideas', 'label' => 'Ideas', 'heading' => 'Ideas we return to for the :concept', 'source' => 'items'],
            ['key' => 'styles', 'label' => 'Styles', 'heading' => 'Style directions available in every :concept', 'source' => 'styles'],
            ['key' => 'interior', 'label' => 'Interior', 'heading' => 'Interior finishes and construction', 'source' => 'items'],
            ['key' => 'accessories', 'label' => 'Accessories', 'heading' => 'Hardware, fittings and integrated detail', 'source' => 'items'],
        ];

        foreach ($tabs as $sort => $tab) {
            ConceptTab::updateOrCreate(
                ['key' => $tab['key']],
                [
                    'label' => ['en' => $tab['label']],
                    'heading_template' => ['en' => $tab['heading']],
                    'source' => $tab['source'],
                    'is_visible' => true,
                    'sort' => $sort,
                ],
            );
        }
    }

    protected function seedConcepts(): void
    {
        // The canvas keys the three item groups by tab; keep that mapping explicit.
        $tabSources = ['ideas' => 'ideas', 'interior' => 'interior', 'accessories' => 'acc'];

        foreach ($this->data['CONCEPTS'] as $sort => $row) {
            $key = Str::slug($row['n']);

            $concept = Concept::updateOrCreate(
                ['key' => $key],
                [
                    'slug' => ['en' => $key],
                    'name' => ['en' => $row['n']],
                    'blurb' => ['en' => $row['d']],
                    'media_id' => $this->media($row['k']),
                    'is_active' => true,
                    'sort' => $sort,
                ],
            );

            $concept->items()->delete();

            foreach ($tabSources as $tab => $sourceKey) {
                foreach ($row[$sourceKey] ?? [] as $index => [$title, $imageKey]) {
                    ConceptItem::create([
                        'concept_id' => $concept->id,
                        'tab' => $tab,
                        'title' => ['en' => $title],
                        'media_id' => $this->media($imageKey),
                        'sort' => $index,
                    ]);
                }
            }
        }
    }

    protected function seedStyles(): void
    {
        foreach ($this->data['STYLES'] as $sort => [$name, $imageKey]) {
            Style::updateOrCreate(
                ['key' => Str::slug($name)],
                [
                    'name' => ['en' => $name],
                    'media_id' => $this->media($imageKey),
                    'is_active' => true,
                    'sort' => $sort,
                ],
            );
        }
    }

    protected function seedMaterials(): void
    {
        $groupStyles = [
            'Wood & Composites' => ['background' => '#ffffff', 'foreground' => '#131936', 'muted' => '#6C7490', 'line' => 'rgba(19,25,54,.14)'],
            'Stones & Minerals' => ['background' => '#EEF3F9', 'foreground' => '#131936', 'muted' => '#5A6280', 'line' => 'rgba(19,25,54,.16)'],
        ];

        $groups = [];
        $groupSort = 0;

        foreach ($this->data['MATERIALS'] as $row) {
            if (isset($groups[$row['g']])) {
                continue;
            }

            $groups[$row['g']] = MaterialGroup::updateOrCreate(
                ['key' => Str::slug($row['g'])],
                array_merge(
                    ['name' => ['en' => $row['g']], 'sort' => $groupSort++],
                    $groupStyles[$row['g']] ?? [],
                ),
            );
        }

        foreach ($this->data['MATERIALS'] as $sort => $row) {
            $material = Material::updateOrCreate(
                ['key' => Str::slug($row['n'])],
                [
                    'slug' => ['en' => Str::slug($row['n'])],
                    'material_group_id' => $groups[$row['g']]->id,
                    'name' => ['en' => $row['n']],
                    'tag' => ['en' => $row['tag']],
                    'blurb' => ['en' => $row['d']],
                    'body' => ['en' => $row['long']],
                    'media_id' => $this->media($row['k']),
                    'is_active' => true,
                    'sort' => $sort,
                ],
            );

            $material->specs()->delete();

            foreach ($row['spec'] as $index => [$label, $value]) {
                MaterialSpec::create([
                    'material_id' => $material->id,
                    'label' => ['en' => $label],
                    'value' => ['en' => $value],
                    'sort' => $index,
                ]);
            }
        }

        // Detail-page gallery: the canvas derived three sibling images by index.
        $materials = Material::orderBy('sort')->get();
        $count = $materials->count();

        foreach ($materials as $index => $material) {
            $material->images()->delete();

            foreach ([0, 1, 2] as $offset) {
                $sibling = $materials[($index + $offset * 3) % $count];

                if (! $sibling->media_id) {
                    continue;
                }

                MaterialImage::create([
                    'material_id' => $material->id,
                    'media_id' => $sibling->media_id,
                    'sort' => $offset,
                ]);
            }
        }
    }

    protected function seedProjects(): void
    {
        foreach (['Residential', 'Commercial'] as $sort => $name) {
            ProjectCategory::updateOrCreate(
                ['key' => Str::slug($name)],
                ['name' => ['en' => $name], 'sort' => $sort],
            );
        }

        foreach (['Completed', 'Ongoing'] as $sort => $name) {
            ProjectStatus::updateOrCreate(
                ['key' => Str::slug($name)],
                ['name' => ['en' => $name], 'sort' => $sort],
            );
        }

        $categories = ProjectCategory::pluck('id', 'key');
        $statuses = ProjectStatus::pluck('id', 'key');

        foreach ($this->data['PROJECTS'] as $sort => $row) {
            $project = Project::updateOrCreate(
                ['key' => Str::slug($row['n'])],
                [
                    'slug' => ['en' => Str::slug($row['n'])],
                    'name' => ['en' => $row['n']],
                    'location' => ['en' => $row['loc']],
                    'project_category_id' => $categories[Str::slug($row['cat'])] ?? null,
                    'project_status_id' => $statuses[Str::slug($row['st'])] ?? null,
                    'year' => $row['yr'],
                    'headline' => ['en' => $row['h']],
                    'body' => ['en' => $row['b']],
                    'media_id' => $this->media($row['k']),
                    'wide_media_id' => $this->media($row['gal'][0] ?? null),
                    'is_featured' => $sort < 3,
                    'is_active' => true,
                    'sort' => $sort,
                ],
            );

            $project->images()->delete();

            // Detail gallery runs gal[1..] then wraps gal[0] to the end.
            $gallery = array_merge(array_slice($row['gal'], 1), [$row['gal'][0]]);

            foreach ($gallery as $index => $imageKey) {
                if (! $mediaId = $this->media($imageKey)) {
                    continue;
                }

                ProjectImage::create([
                    'project_id' => $project->id,
                    'media_id' => $mediaId,
                    'ratio' => $index % 2 === 0 ? '3/4' : '4/5',
                    'sort' => $index,
                ]);
            }
        }
    }

    protected function seedServices(): void
    {
        foreach ($this->data['SERVICES'] as $sort => $row) {
            $service = Service::updateOrCreate(
                ['key' => Str::slug($row['name'])],
                [
                    'number' => $row['num'],
                    'name' => ['en' => $row['name']],
                    'body' => ['en' => $row['body']],
                    'media_id' => $this->media($row['k']),
                    'is_active' => true,
                    'sort' => $sort,
                ],
            );

            $service->points()->delete();

            foreach ($row['points'] as $index => $point) {
                ServicePoint::create([
                    'service_id' => $service->id,
                    'text' => ['en' => $point],
                    'sort' => $index,
                ]);
            }
        }
    }

    /** Wire the project ⇄ material and project ⇄ concept relationships. */
    protected function linkRelations(): void
    {
        $materials = Material::pluck('id', 'key');
        $concepts = Concept::pluck('id', 'key');

        foreach ($this->data['PROJECTS'] as $row) {
            $project = Project::where('key', Str::slug($row['n']))->first();

            if (! $project) {
                continue;
            }

            $project->materials()->sync($this->pivot($row['mats'], $materials));
            $project->concepts()->sync($this->pivot($row['cons'], $concepts));
        }

        // Material detail pages surface three related concepts, mirroring the
        // canvas's index offsets so the seeded site matches the design exactly.
        $conceptIds = Concept::orderBy('sort')->pluck('id')->all();
        $conceptCount = count($conceptIds);

        foreach (Material::orderBy('sort')->get() as $index => $material) {
            $related = [];

            foreach ([0, 3, 6] as $position => $offset) {
                $related[$conceptIds[($index + $offset) % $conceptCount]] = ['sort' => $position];
            }

            $material->concepts()->sync($related);
        }
    }

    protected function pivot(array $names, $lookup): array
    {
        $pivot = [];

        foreach (array_values($names) as $sort => $name) {
            if ($id = $lookup[Str::slug($name)] ?? null) {
                $pivot[$id] = ['sort' => $sort];
            }
        }

        return $pivot;
    }
}

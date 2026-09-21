<?php

namespace Tests\Feature;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Filament\Support\SectionBlocks;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The CMS is used by people who do not write software. These lock in the
 * decisions that keep it that way, because every one of them is easy to undo
 * by accident while adding a field.
 */
class CmsUsabilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_the_dashboard_offers_a_place_to_start(): void
    {
        $html = $this->get(Dashboard::getUrl())->assertOk()->getContent();

        $this->assertStringContainsString('Open the visual editor', $html);
        $this->assertStringContainsString('Upload photos', $html);
        $this->assertStringContainsString('Read enquiries', $html);
    }

    public function test_navigation_uses_plain_language_group_names(): void
    {
        $groups = collect(Filament::getPanel('admin')->getNavigationGroups())
            ->map(fn ($group) => $group->getLabel())
            ->all();

        foreach (['Taxonomy', 'Localisation', 'Structure', 'Library', 'Content'] as $jargon) {
            $this->assertNotContains($jargon, $groups, "“{$jargon}” is not a word an editor would use.");
        }

        $this->assertContains('Website', $groups);
        $this->assertContains('Languages', $groups);
    }

    public function test_a_record_gets_its_reference_name_without_being_asked_for_one(): void
    {
        $page = Page::create([
            'title' => ['en' => 'Our Warranty Terms'],
            'slug' => ['en' => 'warranty'],
        ]);

        $this->assertSame('our-warranty-terms', $page->key);

        // A second record with the same name still gets a usable key.
        $again = Page::create([
            'title' => ['en' => 'Our Warranty Terms'],
            'slug' => ['en' => 'warranty-two'],
        ]);

        $this->assertSame('our-warranty-terms-2', $again->key);

        $project = Project::create([
            'name' => ['en' => 'Erbil Penthouse'],
            'slug' => ['en' => 'erbil-penthouse'],
        ]);

        $this->assertSame('erbil-penthouse', $project->key);
    }

    public function test_a_supplied_reference_name_is_never_overwritten(): void
    {
        $page = Page::create([
            'key' => 'chosen-by-hand',
            'title' => ['en' => 'Anything'],
            'slug' => ['en' => 'anything'],
        ]);

        $this->assertSame('chosen-by-hand', $page->key);
    }

    /**
     * Buttons used to be wired up by typing a page key from memory. Every
     * link field in the block registry must offer a list to choose from.
     */
    public function test_section_links_are_chosen_from_a_list_not_typed(): void
    {
        $linkFields = collect(SectionBlocks::definitions())
            ->flatMap(fn (array $definition) => array_keys($definition['pages'] ?? []))
            ->unique();

        $this->assertNotEmpty($linkFields);

        foreach (SectionBlocks::definitions() as $type => $definition) {
            $components = $this->flatten(SectionBlocks::schema($type));

            foreach (array_keys($definition['pages'] ?? []) as $field) {
                $match = collect($components)->first(
                    fn ($component) => method_exists($component, 'getName')
                        && $component->getName() === "data.{$field}",
                );

                $this->assertInstanceOf(
                    \Filament\Forms\Components\Select::class,
                    $match,
                    "{$type}.{$field} must be a picker, not a free-text key.",
                );
            }
        }
    }

    /**
     * Walk a schema without mounting it — getChildComponents() wants a live
     * Livewire container, which a plain assertion has no reason to build.
     *
     * @param  array<int, mixed>  $components
     * @return array<int, mixed>
     */
    protected function flatten(array $components): array
    {
        $flat = [];

        foreach ($components as $component) {
            $flat[] = $component;

            if (! is_object($component)) {
                continue;
            }

            $reflection = new \ReflectionObject($component);

            if (! $reflection->hasProperty('childComponents')) {
                continue;
            }

            $property = $reflection->getProperty('childComponents');
            $property->setAccessible(true);

            foreach ($property->getValue($component) as $children) {
                if (is_array($children)) {
                    $flat = [...$flat, ...$this->flatten($children)];
                }
            }
        }

        return $flat;
    }

    public function test_no_section_field_asks_for_a_key(): void
    {
        $labels = collect(SectionBlocks::definitions())
            ->flatMap(fn (array $definition) => collect($definition)
                ->filter(fn ($value) => is_array($value))
                ->flatMap(fn (array $fields) => array_values($fields)))
            ->filter(fn ($label) => is_string($label));

        foreach ($labels as $label) {
            $this->assertStringNotContainsStringIgnoringCase(
                'key',
                $label,
                "“{$label}” asks an editor for an identifier.",
            );
        }
    }

    public function test_a_block_explains_what_it_looks_like_before_it_is_chosen(): void
    {
        foreach (SectionBlocks::definitions() as $type => $definition) {
            $this->assertNotEmpty(
                SectionBlocks::hint($type),
                "The “{$definition['label']}” section has nothing describing it.",
            );
        }
    }

    public function test_no_form_asks_an_editor_to_invent_a_system_key(): void
    {
        foreach (Filament::getPanel('admin')->getResources() as $resource) {
            if (! isset($resource::getPages()['create'])) {
                continue;
            }

            $html = $this->get($resource::getUrl('create'))->assertOk()->getContent();

            $this->assertStringNotContainsString(
                'System key',
                $html,
                "{$resource} still asks for a system key.",
            );
        }
    }

    public function test_enquiries_are_an_inbox_rather_than_something_you_create(): void
    {
        $this->assertFalse(FormSubmissionResource::canCreate());
        $this->assertArrayNotHasKey('create', FormSubmissionResource::getPages());
        $this->assertSame('Messages', FormSubmissionResource::getNavigationLabel());
    }

    public function test_searching_finds_a_page_by_the_name_it_was_given(): void
    {
        $page = Page::create([
            'title' => ['en' => 'Warranty And Aftercare'],
            'slug' => ['en' => 'warranty-and-aftercare'],
        ]);

        $results = PageResource::getGlobalSearchResults('Aftercare');

        $this->assertCount(1, $results);
        $this->assertSame('Warranty And Aftercare', $results->first()->title);
        $this->assertSame($page->key, 'warranty-and-aftercare');
    }

    /**
     * The section form is generated from the registry, so a bad entry only
     * shows up when the modal is actually mounted.
     */
    public function test_every_section_on_the_home_page_opens_for_editing(): void
    {
        $page = Page::where('key', 'home')->firstOrFail();

        $this->assertGreaterThan(0, $page->sections()->count());

        foreach ($page->sections as $section) {
            Livewire::test(SectionsRelationManager::class, [
                'ownerRecord' => $page,
                'pageClass' => PageResource::getPages()['edit']->getPage(),
            ])
                ->mountAction('edit', arguments: ['record' => $section->getKey()])
                ->assertHasNoActionErrors();
        }
    }

    public function test_a_button_destination_is_offered_as_a_named_page(): void
    {
        $options = SectionBlocks::pageOptions();

        $this->assertNotEmpty($options);

        // Keyed by what gets stored, labelled by what the editor named it.
        $this->assertArrayHasKey('projects', $options);
        $this->assertSame('Our Projects', $options['projects']);

        foreach ($options as $key => $label) {
            $this->assertNotSame($key, $label, "“{$key}” is shown as its own key.");
        }
    }

    public function test_publishing_a_page_is_a_switch_not_a_timestamp(): void
    {
        $page = Page::whereNotNull('published_at')->firstOrFail();

        Livewire::test(PageResource::getPages()['edit']->getPage(), ['record' => $page->getKey()])
            ->assertFormFieldExists('published')
            ->set('data.published', false)
            ->call('save');

        $this->assertNull($page->fresh()->published_at);
    }
}

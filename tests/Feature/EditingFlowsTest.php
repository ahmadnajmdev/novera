<?php

namespace Tests\Feature;

use App\Filament\Pages\VisualEditor;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Filament\Support\PageLayouts;
use App\Filament\Support\SectionBlocks;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Revision;
use App\Models\Section;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The three things an editor actually does: add a page, add a section to it,
 * and change what is on it. Each one used to span several screens with no
 * signposting between them; these hold the joined-up versions together.
 */
class EditingFlowsTest extends TestCase
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

    // ── Adding a page ───────────────────────────────────────────────────

    public function test_creating_a_page_produces_one_that_is_finished(): void
    {
        $menu = Menu::firstOrFail();

        Livewire::test(PageResource::getPages()['create']->getPage())
            ->set('data.title.en', 'Warranty and aftercare')
            ->set('data.slug.en', 'warranty')
            ->set('data.starting_layout', 'standard')
            ->set('data.add_to_menus', [$menu->id])
            ->set('data.publish_now', true)
            ->call('create')
            ->assertHasNoErrors();

        $page = Page::where('key', 'warranty-and-aftercare')->firstOrFail();

        // Content, not an empty shell.
        $this->assertSame(
            PageLayouts::sections('standard'),
            $page->sections()->orderBy('sort')->pluck('type')->all(),
        );

        // Its own name is already the headline, so there is something to edit.
        $hero = $page->sections()->where('type', 'hero.plain')->firstOrFail();
        $this->assertSame('Warranty and aftercare', $hero->text('heading', 'en'));

        // Visible, and reachable.
        $this->assertTrue($page->isPublished());
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'page_id' => $page->id]);
    }

    public function test_an_empty_start_is_offered_and_stays_empty(): void
    {
        Livewire::test(PageResource::getPages()['create']->getPage())
            ->set('data.title.en', 'Scratch')
            ->set('data.slug.en', 'scratch')
            ->set('data.starting_layout', 'blank')
            ->set('data.publish_now', false)
            ->call('create')
            ->assertHasNoErrors();

        $page = Page::where('key', 'scratch')->firstOrFail();

        $this->assertSame(0, $page->sections()->count());
        $this->assertFalse($page->isPublished());
    }

    public function test_every_starting_layout_builds_sections_the_registry_knows(): void
    {
        $known = array_keys(SectionBlocks::definitions());

        foreach (PageLayouts::all() as $key => $layout) {
            $this->assertNotEmpty($layout['hint'], "Layout “{$key}” does not say what it gives you.");

            foreach ($layout['sections'] as $type) {
                $this->assertContains($type, $known, "Layout “{$key}” builds an unknown section.");
            }
        }
    }

    // ── Adding a section ────────────────────────────────────────────────

    public function test_a_section_can_be_slotted_in_above_another(): void
    {
        $page = Page::where('key', 'home')->firstOrFail();
        $second = $page->sections()->orderBy('sort')->skip(1)->first();
        $before = $page->sections()->orderBy('sort')->pluck('type')->all();

        Livewire::test(SectionsRelationManager::class, [
            'ownerRecord' => $page,
            'pageClass' => PageResource::getPages()['edit']->getPage(),
        ])
            ->callAction(TestAction::make('addAbove')->table($second), data: [
                'type' => 'cta.banner',
                'is_visible' => true,
                'data' => ['heading' => ['en' => 'Slotted *in*']],
            ])
            ->assertHasNoActionErrors();

        $after = $page->sections()->orderBy('sort')->pluck('type')->all();

        // It landed in the gap, and nothing else moved relative to itself.
        array_splice($before, 1, 0, 'cta.banner');
        $this->assertSame($before, $after);
    }

    public function test_the_section_picker_describes_every_choice(): void
    {
        $cards = SectionBlocks::cards();

        $this->assertSame(count(SectionBlocks::definitions()), count($cards));

        foreach ($cards as $type => $card) {
            $this->assertNotEmpty($card['hint'], "“{$type}” has no description on its card.");
            $this->assertNotEmpty($card['group'], "“{$type}” is not filed under a heading.");
            $this->assertStringStartsWith('heroicon-', $card['icon']);
        }

        // Grouped, so seventeen choices read as four short lists.
        $this->assertGreaterThan(1, collect($cards)->pluck('group')->unique()->count());
    }

    // ── Visual editing ──────────────────────────────────────────────────

    public function test_typing_on_the_page_saves_without_a_save_button(): void
    {
        $section = Section::where('type', 'hero.video')->firstOrFail();

        Livewire::test(VisualEditor::class)
            ->call('saveInlineEdit', [
                'model' => 'section',
                'id' => $section->id,
                'field' => 'heading',
                'locale' => 'en',
                'value' => 'Rooms that hold their *value*',
            ]);

        $this->assertSame('Rooms that hold their *value*', $section->fresh()->text('heading', 'en'));

        $this->assertDatabaseHas('revisions', [
            'revisionable_id' => $section->id,
            'source' => 'visual-editor',
        ]);
    }

    public function test_a_picture_can_be_swapped_from_the_page(): void
    {
        $section = Section::where('type', 'intro.split')->firstOrFail();
        $replacement = Media::where('id', '!=', $section->text('media_id'))->firstOrFail();

        Livewire::test(VisualEditor::class)
            ->call('pickMedia', ['model' => 'section', 'id' => $section->id, 'field' => 'media_id'])
            ->assertSet('mediaSelection.field', 'media_id')
            ->call('chooseMedia', $replacement->id);

        $this->assertSame($replacement->id, (int) $section->fresh()->text('media_id'));
    }

    public function test_sections_can_be_reordered_and_hidden_from_the_editor(): void
    {
        $page = Page::where('key', 'home')->firstOrFail();
        $original = $page->sections()->orderBy('sort')->pluck('type')->all();
        $third = $page->sections()->orderBy('sort')->skip(2)->first();

        $editor = Livewire::test(VisualEditor::class)
            ->set('pageId', $page->id)
            ->call('moveSection', $third->id, 'up');

        $moved = $page->sections()->orderBy('sort')->pluck('type')->all();
        $expected = $original;
        [$expected[1], $expected[2]] = [$expected[2], $expected[1]];
        $this->assertSame($expected, $moved);

        $editor->call('toggleSection', $third->id);
        $this->assertFalse($third->fresh()->is_visible);
    }

    /** The preview frame is untrusted: it may only reach the allowlist. */
    public function test_the_preview_frame_cannot_write_outside_the_allowlist(): void
    {
        $section = Section::first();

        Livewire::test(VisualEditor::class)
            ->call('saveInlineEdit', [
                'model' => 'section',
                'id' => $section->id,
                'field' => 'sort',
                'locale' => 'en',
                'value' => '999',
            ]);

        $this->assertNotSame(999, $section->fresh()->sort);

        Livewire::test(VisualEditor::class)
            ->call('pickMedia', ['model' => 'section', 'id' => $section->id, 'field' => 'page_id'])
            ->assertSet('mediaSelection', null);
    }

    public function test_a_picture_must_come_from_the_library(): void
    {
        $section = Section::where('type', 'intro.split')->firstOrFail();
        $before = $section->text('media_id');

        Livewire::test(VisualEditor::class)
            ->call('pickMedia', ['model' => 'section', 'id' => $section->id, 'field' => 'media_id'])
            ->call('chooseMedia', 999999);

        $this->assertSame($before, $section->fresh()->text('media_id'));
    }

    /**
     * A field the CMS lets you type into but the theme never marks is a field
     * you can only reach through the admin form — which is exactly the detour
     * the visual editor exists to remove. Button wording and the big section
     * numerals were unreachable this way until they were marked.
     */
    public function test_every_wording_field_is_reachable_on_the_page(): void
    {
        // Rendered by a Livewire child that receives the section id instead.
        $indirect = ['materials.index' => ['cta_label']];

        foreach (SectionBlocks::definitions() as $type => $definition) {
            $template = base_path(
                'resources/views/site/sections/'.str_replace('.', '-', $type).'.blade.php',
            );

            if (! file_exists($template)) {
                continue;
            }

            $markup = file_get_contents($template);
            $fields = array_merge(
                array_keys($definition['text'] ?? []),
                array_keys($definition['area'] ?? []),
            );

            foreach ($fields as $field) {
                if (in_array($field, $indirect[$type] ?? [], true)) {
                    continue;
                }

                $this->assertStringContainsString(
                    "nv_edit('section', \$section->id, '{$field}')",
                    $markup,
                    "“{$field}” on {$type} cannot be edited on the page.",
                );
            }
        }
    }

    public function test_every_picture_slot_is_reachable_on_the_page(): void
    {
        foreach (SectionBlocks::definitions() as $type => $definition) {
            $template = base_path(
                'resources/views/site/sections/'.str_replace('.', '-', $type).'.blade.php',
            );

            if (! file_exists($template) || empty($definition['media'])) {
                continue;
            }

            $markup = file_get_contents($template);

            foreach (array_keys($definition['media']) as $field) {
                $this->assertStringContainsString(
                    "nv_media_edit('section', \$section->id, '{$field}')",
                    $markup,
                    "The picture “{$field}” on {$type} cannot be swapped on the page.",
                );
            }
        }
    }

    public function test_the_editor_marks_up_text_and_pictures_for_the_frame(): void
    {
        $page = Page::where('key', 'home')->firstOrFail();

        $html = $this->get(nv_url($page, 'en').'?'.config('novera.edit_param').'=1')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('data-nv-edit="section:', $html);
        $this->assertStringContainsString('data-nv-media="section:', $html);
        $this->assertStringContainsString('nv-editing', $html);
    }
}

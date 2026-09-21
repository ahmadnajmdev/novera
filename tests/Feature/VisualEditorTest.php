<?php

namespace Tests\Feature;

use App\Filament\Pages\VisualEditor;
use App\Models\Concept;
use App\Models\Page;
use App\Models\Revision;
use App\Models\Section;
use App\Models\User;
use App\Models\UserRole;
use App\Support\InlineEditor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class VisualEditorTest extends TestCase
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

    protected function heroSection(): Section
    {
        return Page::where('key', 'home')->firstOrFail()->sections()->where('type', 'hero.video')->firstOrFail();
    }

    public function test_selecting_a_block_field_loads_the_stored_copy(): void
    {
        $section = $this->heroSection();

        Livewire::test(VisualEditor::class)
            ->call('selectNode', [
                'model' => 'section',
                'id' => $section->id,
                'field' => 'heading',
                'locale' => 'en',
            ])
            ->assertSet('value', 'Crafting spaces that define *living*');
    }

    public function test_saving_updates_the_block_and_shows_on_the_site(): void
    {
        $section = $this->heroSection();

        Livewire::test(VisualEditor::class)
            ->call('selectNode', ['model' => 'section', 'id' => $section->id, 'field' => 'heading', 'locale' => 'en'])
            ->set('value', 'Rooms that define *living*')
            ->call('save')
            ->assertDispatched('nv-apply');

        $this->assertSame(
            'Rooms that define *living*',
            $section->fresh()->data['heading']['en'],
        );

        $this->get('/en')->assertOk()->assertSee('Rooms that define');
    }

    public function test_editing_a_non_default_locale_leaves_the_source_intact(): void
    {
        $section = $this->heroSection();

        Livewire::test(VisualEditor::class)
            ->set('locale', 'ku')
            ->call('selectNode', ['model' => 'section', 'id' => $section->id, 'field' => 'lead', 'locale' => 'ku'])
            ->set('value', 'نووسینی نوێ')
            ->call('save');

        $data = $section->fresh()->data;

        $this->assertSame('نووسینی نوێ', $data['lead']['ku']);
        $this->assertStringContainsString('One studio draws it', $data['lead']['en']);
        $this->get('/ku')->assertOk()->assertSee('نووسینی نوێ');
    }

    public function test_catalogue_records_are_editable_too(): void
    {
        $concept = Concept::where('key', 'kitchen')->firstOrFail();

        Livewire::test(VisualEditor::class)
            ->call('selectNode', ['model' => 'concept', 'id' => $concept->id, 'field' => 'name', 'locale' => 'en'])
            ->set('value', 'Kitchens')
            ->call('save');

        $this->assertSame('Kitchens', $concept->fresh()->getTranslation('name', 'en'));
    }

    public function test_every_save_is_recorded_as_a_revision(): void
    {
        $section = $this->heroSection();

        Livewire::test(VisualEditor::class)
            ->call('selectNode', ['model' => 'section', 'id' => $section->id, 'field' => 'eyebrow', 'locale' => 'en'])
            ->set('value', 'Design · Manufacture · Install')
            ->call('save');

        $revision = Revision::latest('id')->firstOrFail();

        $this->assertSame('visual-editor', $revision->source);
        $this->assertSame($this->admin->id, $revision->user_id);
        $this->assertSame('Design · Manufacture · Install', $revision->after['eyebrow']['en']);
    }

    public function test_writes_are_confined_to_an_allowlist(): void
    {
        $editor = app(InlineEditor::class);
        $section = $this->heroSection();

        $this->expectException(InvalidArgumentException::class);
        $editor->write('section', $section->id, 'settings', 'en', 'nope');
    }

    public function test_arbitrary_models_cannot_be_reached(): void
    {
        $editor = app(InlineEditor::class);

        $this->expectException(InvalidArgumentException::class);
        $editor->write('user', $this->admin->id, 'password', 'en', 'hunter2');
    }

    public function test_non_translatable_columns_are_rejected(): void
    {
        $editor = app(InlineEditor::class);
        $concept = Concept::first();

        $this->expectException(InvalidArgumentException::class);
        $editor->write('concept', $concept->id, 'key', 'en', 'hacked');
    }

    public function test_editors_cannot_reach_administrator_screens(): void
    {
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => bcrypt('secret'),
            'role' => UserRole::Editor,
            'is_active' => true,
        ]);

        $this->actingAs($editor);

        $this->assertFalse(\App\Filament\Resources\Users\UserResource::canAccess());
        $this->assertFalse(\App\Filament\Resources\Locales\LocaleResource::canAccess());
        $this->assertTrue(\App\Filament\Resources\Concepts\ConceptResource::canAccess());
    }
}

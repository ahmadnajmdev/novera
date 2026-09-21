<?php

namespace Tests\Feature;

use App\Livewire\ConceptTabs;
use App\Livewire\EnquiryForm;
use App\Livewire\MaterialIndex;
use App\Livewire\ProjectIndex;
use App\Models\Concept;
use App\Models\ContentCollection;
use App\Models\ContentItem;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\Locale;
use App\Models\Material;
use App\Models\Setting;
use App\Support\Settings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class DynamicContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_enquiry_form_is_built_from_the_database(): void
    {
        Mail::fake();

        Livewire::test(EnquiryForm::class, ['formKey' => 'enquiry'])
            ->set('values.name', 'Sara Ahmed')
            ->set('values.phone', '+964 750 111 2222')
            ->set('values.email', 'sara@example.com')
            ->set('values.project_type', 'residence')
            ->set('values.message', 'A full kitchen and two dressing rooms.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $submission = FormSubmission::latest('id')->firstOrFail();

        $this->assertSame('Sara Ahmed', $submission->payload['name']);
        $this->assertSame('residence', $submission->payload['project_type']);
    }

    public function test_required_fields_are_enforced_from_the_database(): void
    {
        Livewire::test(EnquiryForm::class, ['formKey' => 'enquiry'])
            ->set('values.name', '')
            ->call('submit')
            ->assertHasErrors(['values.name']);
    }

    public function test_adding_a_field_in_the_cms_adds_it_to_the_form(): void
    {
        $form = Form::where('key', 'enquiry')->firstOrFail();

        FormField::create([
            'form_id' => $form->id,
            'key' => 'budget',
            'type' => 'text',
            'label' => ['en' => 'Budget'],
            'is_required' => true,
            'is_visible' => true,
            'sort' => 10,
        ]);

        Livewire::test(EnquiryForm::class, ['formKey' => 'enquiry'])
            ->assertSee('Budget')
            ->set('values.name', 'A')->set('values.phone', 'B')->set('values.email', 'a@b.co')
            ->call('submit')
            ->assertHasErrors(['values.budget']);
    }

    public function test_the_honeypot_silently_absorbs_bots(): void
    {
        Livewire::test(EnquiryForm::class, ['formKey' => 'enquiry'])
            ->set('company', 'spam-bot')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertSame(0, FormSubmission::count());
    }

    public function test_project_filters_come_from_content_and_filter_the_grid(): void
    {
        Livewire::test(ProjectIndex::class)
            ->assertSee('Villa Sarwaran')
            ->call('select', 'status:ongoing')
            ->assertSee('Zagros Retreat')
            ->assertDontSee('Villa Sarwaran');
    }

    public function test_a_new_filter_added_in_the_cms_works_immediately(): void
    {
        $collection = ContentCollection::where('key', 'project_filters')->firstOrFail();

        ContentItem::create([
            'content_collection_id' => $collection->id,
            'label' => ['en' => 'Library'],
            'extra' => ['type' => 'concept', 'match' => 'library'],
            'is_active' => true,
            'sort' => 99,
        ]);

        Livewire::test(ProjectIndex::class)
            ->assertSee('Library')
            ->call('select', 'concept:library')
            ->assertSee('Dream City Residence')
            ->assertDontSee('Villa Sarwaran');
    }

    public function test_concept_tabs_switch_their_source(): void
    {
        $concept = Concept::where('key', 'kitchen')->firstOrFail();

        Livewire::test(ConceptTabs::class, ['concept' => $concept])
            ->assertSee('Island-led open plan')
            ->call('select', 'styles')
            // The styles tab is fed by the shared style list, not per-concept items.
            ->assertSee('Modern Classic')
            ->call('select', 'accessories')
            ->assertSee('Push-to-open hardware');
    }

    public function test_the_material_index_swaps_its_preview(): void
    {
        $onyx = Material::where('key', 'onyx')->firstOrFail();

        Livewire::test(MaterialIndex::class)
            ->call('highlight', $onyx->id)
            ->assertSee('Translucent stone, at its best when backlit.');
    }

    public function test_theme_colours_are_driven_by_settings(): void
    {
        Setting::where('key', 'theme.gold')->update(['value' => json_encode('#FF0000')]);
        app(Settings::class)->flush();

        $this->get('/en')->assertOk()->assertSee('--nv-gold: #FF0000', escape: false);
    }

    public function test_motion_can_be_switched_off_from_settings(): void
    {
        $this->get('/en')->assertOk()->assertSee('nv-boot');

        Setting::where('key', 'motion.boot')->update(['value' => json_encode(false)]);
        app(Settings::class)->flush();

        $this->get('/en')->assertOk()->assertDontSee('nv-boot');
    }

    public function test_adding_a_locale_makes_it_routable(): void
    {
        Locale::create([
            'code' => 'tr',
            'name' => 'Turkish',
            'native_name' => 'Türkçe',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort' => 3,
        ]);

        $this->get('/tr')->assertOk()->assertSee('lang="tr"', escape: false);
    }

    public function test_menu_changes_appear_in_the_header(): void
    {
        $this->get('/en')->assertOk()->assertSee('Materials');

        \App\Models\MenuItem::whereHas('menu', fn ($q) => $q->where('key', 'primary'))
            ->whereHas('page', fn ($q) => $q->where('key', 'materials'))
            ->update(['is_visible' => false]);

        $this->get('/en')->assertOk()->assertDontSee('>Materials<', escape: false);
    }
}

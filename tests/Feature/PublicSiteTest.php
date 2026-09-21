<?php

namespace Tests\Feature;

use App\Models\Concept;
use App\Models\Material;
use App\Models\Page;
use App\Models\Project;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_root_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_every_page_renders_in_every_locale(): void
    {
        foreach (['en', 'ku', 'ar'] as $locale) {
            foreach (Page::all() as $page) {
                $this->get(nv_url($page, $locale))
                    ->assertOk()
                    ->assertSee('</html>', escape: false);
            }
        }
    }

    public function test_detail_pages_render(): void
    {
        $this->get(nv_entity_url(Concept::first(), 'en'))->assertOk();
        $this->get(nv_entity_url(Project::first(), 'en'))->assertOk();
        $this->get(nv_entity_url(Material::first(), 'en'))->assertOk();
    }

    public function test_rtl_locales_set_direction_and_script_flag(): void
    {
        $this->get('/ku')
            ->assertOk()
            ->assertSee('dir="rtl"', escape: false)
            ->assertSee('data-ar="1"', escape: false);

        $this->get('/en')
            ->assertOk()
            ->assertSee('dir="ltr"', escape: false)
            ->assertDontSee('data-ar="1"', escape: false);
    }

    public function test_content_is_translated_rather_than_falling_back_to_english(): void
    {
        $this->get('/ku')->assertOk()->assertSee('دروستکردنی شوێنانێک کە پێناسەن بۆ');
        $this->get('/ar')->assertOk()->assertSee('نصنع مساحاتٍ تُعرِّف');
    }

    public function test_pattern_translations_substitute_the_concept_name(): void
    {
        $kitchen = Concept::where('key', 'kitchen')->firstOrFail();

        // "Ideas we return to for the kitchen" has no literal dictionary entry;
        // it resolves through a regex pattern with the room name folded in.
        $this->get(nv_entity_url($kitchen, 'ku'))
            ->assertOk()
            ->assertSee('بیرۆکەکان کە بۆ چێشتخانە دووبارە دەیانکەینەوە');
    }

    public function test_unknown_paths_404_and_missing_locale_redirects(): void
    {
        $this->get('/en/nope')->assertNotFound();
        $this->get('/about')->assertRedirect('/en/about');
    }

    public function test_draft_pages_are_hidden_from_visitors(): void
    {
        $page = Page::where('key', 'about')->firstOrFail();
        $page->update(['published_at' => null]);

        $this->get(nv_url($page, 'en'))->assertNotFound();
    }

    public function test_edit_mode_markers_require_authentication(): void
    {
        $this->get('/en?nv-edit=1')->assertOk()->assertDontSee('nv-editing');

        $this->actingAs(\App\Models\User::first())
            ->get('/en?nv-edit=1')
            ->assertOk()
            ->assertSee('nv-editing');
    }
}

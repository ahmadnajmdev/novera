<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\Translation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_editing_a_setting_shows_without_a_manual_cache_clear(): void
    {
        $this->get('/en')->assertOk()->assertSee('Erbil showroom');

        Setting::where('key', 'contact.menu_line')->firstOrFail()
            ->update(['value' => ['en' => 'Sulaymaniyah showroom · +964 770 000 0000']]);

        $this->get('/en')->assertOk()->assertSee('Sulaymaniyah showroom');
    }

    public function test_contact_details_have_a_single_source_of_truth(): void
    {
        // Contact rows are the one editable place; no duplicate scalar settings.
        $this->assertSame(0, Setting::whereIn('key', [
            'contact.phone', 'contact.email', 'contact.address', 'contact.hours',
        ])->count());

        $email = \App\Models\ContentItem::whereHas('collection', fn ($q) => $q->where('key', 'contact_rows'))
            ->where('key', 'email')
            ->firstOrFail();

        $this->get('/en')->assertOk()->assertSee('info@noverainteriors.com');

        $email->setTranslation('value', 'en', 'hello@novera.iq')->save();

        $this->get('/en')->assertOk()->assertSee('hello@novera.iq');
    }

    public function test_editing_a_translation_shows_immediately(): void
    {
        $translation = Translation::where('source', 'Contact')->firstOrFail();
        $translation->update(['values' => array_merge($translation->values, ['ku' => 'پەیوەندیی نوێ'])]);

        $this->get('/ku')->assertOk()->assertSee('پەیوەندیی نوێ');
    }

    public function test_renaming_a_slug_moves_the_page(): void
    {
        $page = Page::where('key', 'about')->firstOrFail();

        $this->get('/en/about')->assertOk();

        $page->setTranslation('slug', 'en', 'studio')->save();

        $this->get('/en/studio')->assertOk();
        $this->get('/en/about')->assertNotFound();
        $this->get('/en')->assertOk()->assertSee('/en/studio', escape: false);
    }

    public function test_a_redirect_added_in_the_cms_takes_effect(): void
    {
        $this->get('/en/old-work')->assertNotFound();

        Redirect::create(['from' => '/en/old-work', 'to' => '/en/projects', 'status' => 301, 'is_active' => true]);

        $this->get('/en/old-work')->assertRedirect('/en/projects');
    }
}

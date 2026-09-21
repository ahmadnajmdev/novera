<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\Settings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::firstOrFail());
    }

    /**
     * Each settings group gets one locale tab set holding all of its
     * translatable fields. Building one per field turned a group into a stack
     * of identical EN/KU/AR widgets.
     */
    public function test_each_group_renders_a_single_locale_tab_set(): void
    {
        $html = Livewire::test(ManageSettings::class)->html();

        preg_match_all('/fi-tabs-item-label[^>]*>\s*([^<]{1,40})\s*</', $html, $matches);
        $labels = array_map('trim', $matches[1]);

        $localeTabs = array_filter($labels, fn (string $label) => str_contains($label, '·'));
        $groupsWithCopy = Setting::where('is_translatable', true)->distinct()->count('group');

        $this->assertSame(
            $groupsWithCopy,
            count($localeTabs) / 3,
            'Expected one EN/KU/AR tab set per group that has translatable settings.',
        );
    }

    public function test_groups_without_translatable_settings_have_no_locale_tabs(): void
    {
        // Theme is 14 colour and typography values, none of them translated.
        $this->assertSame(0, Setting::where('group', 'theme')->where('is_translatable', true)->count());

        $html = Livewire::test(ManageSettings::class)->html();

        // Groups are shown under the name an editor would use, not the
        // database's own.
        $this->assertStringContainsString('Colours &amp; type', $html);
    }

    public function test_saving_writes_both_translatable_and_plain_settings(): void
    {
        Livewire::test(ManageSettings::class)
            ->set('data.brand__tagline.en', 'One team, drawing to handover.')
            ->set('data.brand__tagline.ku', 'یەک تیم، لە نەخشە تا ڕادەستکردن.')
            ->set('data.theme__gold', '#E0C070')
            ->set('data.motion__boot', false)
            ->call('save')
            ->assertHasNoErrors();

        app(Settings::class)->flush();

        $tagline = Setting::where('key', 'brand.tagline')->firstOrFail()->value;

        $this->assertSame('One team, drawing to handover.', $tagline['en']);
        $this->assertSame('یەک تیم، لە نەخشە تا ڕادەستکردن.', $tagline['ku']);
        $this->assertSame('#E0C070', Setting::where('key', 'theme.gold')->firstOrFail()->value);
        $this->assertFalse(Setting::where('key', 'motion.boot')->firstOrFail()->value);
    }

    public function test_saved_settings_reach_the_public_site(): void
    {
        Livewire::test(ManageSettings::class)
            ->set('data.theme__gold', '#E0C070')
            ->set('data.motion__boot', false)
            ->call('save');

        $this->get('/en')
            ->assertOk()
            ->assertSee('--nv-gold: #E0C070', escape: false)
            ->assertDontSee('nv-boot');
    }

    /** The group tabs and the locale tabs must not share a query-string key. */
    public function test_the_two_tab_sets_use_different_query_keys(): void
    {
        $page = file_get_contents(app_path('Filament/Pages/ManageSettings.php'));
        $helper = file_get_contents(app_path('Filament/Support/Translatable.php'));

        $this->assertStringContainsString("persistTabInQueryString('group')", $page);
        $this->assertStringContainsString("persistTabInQueryString('locale')", $helper);
    }
}

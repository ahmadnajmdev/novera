<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::firstOrFail();
    }

    public function test_the_panel_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_every_resource_list_and_create_screen_loads(): void
    {
        $this->actingAs($this->admin);

        $resources = Filament::getPanel('admin')->getResources();

        $this->assertNotEmpty($resources);

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            if (isset($pages['index'])) {
                $this->get($resource::getUrl('index'))
                    ->assertOk("Index screen failed for {$resource}");
            }

            if (isset($pages['create'])) {
                $this->get($resource::getUrl('create'))
                    ->assertOk("Create screen failed for {$resource}");
            }
        }
    }

    public function test_every_resource_edit_screen_loads_for_a_real_record(): void
    {
        $this->actingAs($this->admin);

        foreach (Filament::getPanel('admin')->getResources() as $resource) {
            if (! isset($resource::getPages()['edit'])) {
                continue;
            }

            $record = $resource::getModel()::query()->first();

            if (! $record) {
                continue;
            }

            $this->get($resource::getUrl('edit', ['record' => $record]))
                ->assertOk("Edit screen failed for {$resource}");
        }
    }

    public function test_custom_pages_load(): void
    {
        $this->actingAs($this->admin);

        $this->get(\App\Filament\Pages\VisualEditor::getUrl())->assertOk();
        $this->get(\App\Filament\Pages\ManageSettings::getUrl())->assertOk();
    }
}

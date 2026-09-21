<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $pages = Page::pluck('id', 'key');

        $menus = [
            'primary' => [
                'name' => 'Header navigation',
                'items' => [
                    ['label' => 'Projects', 'page' => 'projects'],
                    ['label' => 'Concepts', 'page' => 'concepts'],
                    ['label' => 'Materials', 'page' => 'materials'],
                    ['label' => 'Services', 'page' => 'services'],
                    ['label' => 'About', 'page' => 'about'],
                ],
            ],
            'index' => [
                'name' => 'Index overlay',
                'items' => [
                    ['label' => 'Home', 'page' => 'home'],
                    ['label' => 'About Us', 'page' => 'about'],
                    ['label' => 'Concepts', 'page' => 'concepts'],
                    ['label' => 'Our Projects', 'page' => 'projects'],
                    ['label' => 'Our Materials', 'page' => 'materials'],
                    ['label' => 'Services', 'page' => 'services'],
                    ['label' => 'Contact Us', 'page' => 'contact'],
                ],
            ],
            'footer' => [
                'name' => 'Footer navigation',
                'items' => [
                    ['label' => 'Home', 'page' => 'home'],
                    ['label' => 'About Us', 'page' => 'about'],
                    ['label' => 'Concepts', 'page' => 'concepts'],
                    ['label' => 'Our Projects', 'page' => 'projects'],
                    ['label' => 'Our Materials', 'page' => 'materials'],
                    ['label' => 'Services', 'page' => 'services'],
                    ['label' => 'Contact Us', 'page' => 'contact'],
                ],
            ],
        ];

        foreach ($menus as $key => $definition) {
            $menu = Menu::updateOrCreate(['key' => $key], ['name' => $definition['name']]);
            $menu->allItems()->delete();

            foreach ($definition['items'] as $sort => $item) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'label' => ['en' => $item['label']],
                    'page_id' => $pages[$item['page']] ?? null,
                    'sort' => $sort,
                    'is_visible' => true,
                ]);
            }
        }
    }
}

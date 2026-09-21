<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use App\Support\Settings;
use App\Support\Site;
use App\Support\SiteTranslator;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocaleSeeder::class,
            SettingSeeder::class,
            TranslationSeeder::class,
            MediaSeeder::class,
            CatalogSeeder::class,
            ContentCollectionSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
            FormSeeder::class,
        ]);

        User::firstOrCreate(
            ['email' => 'admin@noverainteriors.com'],
            [
                'name' => 'Novera Admin',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        app(Settings::class)->flush();
        app(SiteTranslator::class)->flush();
        app(Site::class)->flush();
    }
}

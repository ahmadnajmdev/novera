<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'EN',
                'direction' => 'ltr',
                'heading_font' => "'Cinzel', Georgia, serif",
                'body_font' => "'Hanken Grotesk', system-ui, sans-serif",
                'is_default' => true,
                'sort' => 0,
            ],
            [
                'code' => 'ku',
                'name' => 'Kurdish (Sorani)',
                'native_name' => 'کوردی',
                'direction' => 'rtl',
                'heading_font' => "'Novera Arabic', 'Cinzel', serif",
                'body_font' => "'Noto Naskh Arabic', sans-serif",
                'is_default' => false,
                'sort' => 1,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'عربي',
                'direction' => 'rtl',
                'heading_font' => "'Novera Arabic', 'Cinzel', serif",
                'body_font' => "'Noto Naskh Arabic', sans-serif",
                'is_default' => false,
                'sort' => 2,
            ],
        ];

        foreach ($locales as $locale) {
            Locale::updateOrCreate(['code' => $locale['code']], $locale);
        }
    }
}

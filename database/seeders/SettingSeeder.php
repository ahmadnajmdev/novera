<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->definitions() as $sort => $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['sort' => $sort]),
            );
        }

        app(Settings::class)->flush();
    }

    protected function definitions(): array
    {
        return [
            // ── Brand ────────────────────────────────────────────────
            ['group' => 'brand', 'key' => 'brand.name', 'label' => 'Brand name', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Novera Interiors', 'ku' => 'نۆڤێرا ئینتیریۆرز', 'ar' => 'نوفيرا للتصميم الداخلي']],
            ['group' => 'brand', 'key' => 'brand.legal', 'label' => 'Legal name', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Novera Interiors®']],
            ['group' => 'brand', 'key' => 'brand.tagline', 'label' => 'Footer tagline', 'type' => 'textarea', 'is_translatable' => true,
                'value' => ['en' => 'Design, premium materials, manufacturing and installation — delivered by one team.']],
            ['group' => 'brand', 'key' => 'brand.copyright', 'label' => 'Copyright line', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => '© 2026 Novera Interiors®. All rights reserved.']],
            ['group' => 'brand', 'key' => 'brand.credit', 'label' => 'Footer credit', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Placeholder photography via Unsplash & Pexels — to be replaced with Novera\'s own work.']],
            ['group' => 'brand', 'key' => 'brand.logo_gold', 'label' => 'Logo — gold', 'type' => 'media', 'value' => null],
            ['group' => 'brand', 'key' => 'brand.logo_navy', 'label' => 'Logo — navy', 'type' => 'media', 'value' => null],
            ['group' => 'brand', 'key' => 'brand.logo_vertical', 'label' => 'Logo — vertical gold', 'type' => 'media', 'value' => null],

            // ── Contact ──────────────────────────────────────────────
            // Phone, email, address and hours live as rows in the
            // "contact_rows" collection, which is what the footer and contact
            // page render. Keeping a second copy here would mean two places to
            // edit one phone number.
            ['group' => 'contact', 'key' => 'contact.menu_line', 'label' => 'Index overlay contact line', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Erbil showroom · +964 750 000 0000 · info@noverainteriors.com']],
            ['group' => 'contact', 'key' => 'contact.map_label', 'label' => 'Map caption', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Showroom — Erbil · Map']],
            ['group' => 'contact', 'key' => 'contact.map_url', 'label' => 'Map link', 'type' => 'text', 'value' => null],

            // ── Theme tokens ─────────────────────────────────────────
            ['group' => 'theme', 'key' => 'theme.ink', 'label' => 'Ink', 'type' => 'color', 'value' => '#0B0F22'],
            ['group' => 'theme', 'key' => 'theme.navy', 'label' => 'Novera navy', 'type' => 'color', 'value' => '#131936'],
            ['group' => 'theme', 'key' => 'theme.gold', 'label' => 'Luxury gold', 'type' => 'color', 'value' => '#F2D9A0'],
            ['group' => 'theme', 'key' => 'theme.gold_deep', 'label' => 'Deep gold', 'type' => 'color', 'value' => '#8A6A16'],
            ['group' => 'theme', 'key' => 'theme.light', 'label' => 'Light blue', 'type' => 'color', 'value' => '#EEF3F9'],
            ['group' => 'theme', 'key' => 'theme.body', 'label' => 'Body text', 'type' => 'color', 'value' => '#39415F'],
            ['group' => 'theme', 'key' => 'theme.muted', 'label' => 'Muted text', 'type' => 'color', 'value' => '#6C7490'],
            ['group' => 'theme', 'key' => 'theme.white', 'label' => 'White', 'type' => 'color', 'value' => '#FFFFFF'],
            ['group' => 'theme', 'key' => 'theme.radius', 'label' => 'Corner radius', 'type' => 'text', 'value' => '16px'],
            ['group' => 'theme', 'key' => 'theme.max_width', 'label' => 'Container width', 'type' => 'text', 'value' => '1680px'],
            ['group' => 'theme', 'key' => 'theme.heading_font', 'label' => 'Heading font stack', 'type' => 'text', 'value' => "'Cinzel', Georgia, serif"],
            ['group' => 'theme', 'key' => 'theme.body_font', 'label' => 'Body font stack', 'type' => 'text', 'value' => "'Hanken Grotesk', system-ui, sans-serif"],
            ['group' => 'theme', 'key' => 'theme.arabic_heading_font', 'label' => 'Arabic heading stack', 'type' => 'text', 'value' => "'Novera Arabic', 'Cinzel', serif"],
            ['group' => 'theme', 'key' => 'theme.arabic_body_font', 'label' => 'Arabic body stack', 'type' => 'text', 'value' => "'Noto Naskh Arabic', sans-serif"],

            // ── Motion ───────────────────────────────────────────────
            ['group' => 'motion', 'key' => 'motion.reveal', 'label' => 'Scroll reveal', 'type' => 'boolean', 'value' => true],
            ['group' => 'motion', 'key' => 'motion.parallax', 'label' => 'Parallax imagery', 'type' => 'boolean', 'value' => true],
            ['group' => 'motion', 'key' => 'motion.smooth_scroll', 'label' => 'Eased scrolling', 'type' => 'boolean', 'value' => true],
            ['group' => 'motion', 'key' => 'motion.marquee', 'label' => 'Hero marquee', 'type' => 'boolean', 'value' => true],
            ['group' => 'motion', 'key' => 'motion.boot', 'label' => 'Boot splash', 'type' => 'boolean', 'value' => true],
            ['group' => 'motion', 'key' => 'motion.rail_autoscroll', 'label' => 'Concept rail auto-scroll', 'type' => 'boolean', 'value' => true],

            // ── Hero ─────────────────────────────────────────────────
            ['group' => 'hero', 'key' => 'hero.video_url', 'label' => 'Hero video (mp4)', 'type' => 'text',
                'value' => 'https://videos.pexels.com/video-files/31617692/13470975_1920_1080_24fps.mp4'],
            ['group' => 'hero', 'key' => 'hero.video_poster', 'label' => 'Hero poster image', 'type' => 'text',
                'value' => 'https://images.pexels.com/videos/31617692/luxury-kitchen-mansion-new-homes-remodeling-31617692.jpeg?auto=compress&cs=tinysrgb&w=1600'],

            // ── SEO ──────────────────────────────────────────────────
            ['group' => 'seo', 'key' => 'seo.title_suffix', 'label' => 'Title suffix', 'type' => 'text', 'is_translatable' => true,
                'value' => ['en' => 'Novera Interiors']],
            ['group' => 'seo', 'key' => 'seo.default_description', 'label' => 'Default description', 'type' => 'textarea', 'is_translatable' => true,
                'value' => ['en' => 'Interior design, premium materials, in-house manufacturing and installation across the Kurdistan Region of Iraq.']],
            ['group' => 'seo', 'key' => 'seo.og_image', 'label' => 'Default share image', 'type' => 'media', 'value' => null],
        ];
    }
}

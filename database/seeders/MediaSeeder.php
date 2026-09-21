<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBrand();
        $this->seedPlaceholders();
    }

    /** Brand marks live on the public disk so they can be swapped in the CMS. */
    protected function seedBrand(): void
    {
        $brand = [
            'brand.logo_gold' => ['brand/novera-gold.png', 'Novera Interiors — gold wordmark'],
            'brand.logo_navy' => ['brand/novera-navy.png', 'Novera Interiors — navy wordmark'],
            'brand.logo_vertical' => ['brand/novera-vertical-gold.png', 'Novera Interiors — vertical gold mark'],
        ];

        foreach ($brand as $settingKey => [$path, $alt]) {
            $absolute = storage_path('app/public/'.$path);
            $size = is_file($absolute) ? filesize($absolute) : null;
            [$width, $height] = is_file($absolute) ? (getimagesize($absolute) ?: [null, null]) : [null, null];

            $media = Media::updateOrCreate(
                ['path' => $path],
                [
                    'disk' => 'public',
                    'filename' => basename($path),
                    'mime_type' => 'image/png',
                    'size' => $size,
                    'width' => $width,
                    'height' => $height,
                    'alt' => ['en' => $alt],
                    'folder' => 'brand',
                ],
            );

            Setting::where('key', $settingKey)->update(['value' => json_encode($media->id)]);
        }

        app(Settings::class)->flush();
    }

    /**
     * The design ships with Unsplash placeholders keyed by a short name. They
     * are stored as remote media so the client can replace each one with their
     * own photography without touching a template.
     */
    protected function seedPlaceholders(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/design.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['ID'] as $key => $photoId) {
            Media::updateOrCreate(
                ['external_url' => 'https://images.unsplash.com/photo-'.$photoId],
                [
                    'disk' => 'remote',
                    'filename' => $key,
                    'mime_type' => 'image/jpeg',
                    'alt' => ['en' => \Illuminate\Support\Str::headline($key)],
                    'folder' => 'placeholders',
                ],
            );
        }

        $this->command?->info('Registered '.count($data['ID']).' placeholder images.');
    }
}

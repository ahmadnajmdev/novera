<?php

namespace Database\Seeders;

use App\Models\ContentCollection;
use App\Models\ContentItem;
use Illuminate\Database\Seeder;

class ContentCollectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->collections() as $definition) {
            $collection = ContentCollection::updateOrCreate(
                ['key' => $definition['key']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'schema' => $definition['schema'] ?? null,
                ],
            );

            $collection->items()->delete();

            foreach ($definition['items'] as $sort => $item) {
                ContentItem::create(array_merge([
                    'content_collection_id' => $collection->id,
                    'sort' => $sort,
                    'is_active' => true,
                ], $item));
            }
        }
    }

    protected function collections(): array
    {
        return [
            [
                'key' => 'stats',
                'name' => 'Home statistics',
                'description' => 'The three big figures under the introduction on the home page.',
                'schema' => ['label' => 'Figure', 'value' => 'Caption'],
                'items' => [
                    ['label' => ['en' => '14'], 'value' => ['en' => 'Material families']],
                    ['label' => ['en' => '10'], 'value' => ['en' => 'Concept categories']],
                    ['label' => ['en' => '01'], 'value' => ['en' => 'Team, end to end']],
                ],
            ],
            [
                'key' => 'standards',
                'name' => 'Our standards',
                'description' => 'The list of standards on the About page — a heading and a line each.',
                'schema' => ['label' => 'Standard', 'value' => 'Note'],
                'items' => [
                    ['label' => ['en' => 'Quality'], 'value' => ['en' => 'Signed off in the factory, not on site.']],
                    ['label' => ['en' => 'Precision'], 'value' => ['en' => 'Machined tolerances in millimetres.']],
                    ['label' => ['en' => 'Materials'], 'value' => ['en' => 'Selected slab by slab before specification.']],
                    ['label' => ['en' => 'Craftsmanship'], 'value' => ['en' => 'Hand-finished at every visible edge.']],
                    ['label' => ['en' => 'Detail'], 'value' => ['en' => 'Shadow gaps drawn, never improvised.']],
                    ['label' => ['en' => 'Execution'], 'value' => ['en' => 'One team from drawing to handover.']],
                ],
            ],
            [
                'key' => 'facilities',
                'name' => 'Facilities',
                'description' => 'The showroom, material wall, factory and installation cards on the About page.',
                'schema' => ['label' => 'Name', 'value' => 'Note', 'extra.tag' => 'Eyebrow'],
                'items' => [
                    ['label' => ['en' => 'Full-scale rooms'], 'value' => ['en' => 'Complete kitchens, dressing rooms and bathrooms you can open, close and stand inside.'], 'extra' => ['tag' => ['en' => 'Showroom']]],
                    ['label' => ['en' => 'Fourteen families'], 'value' => ['en' => 'Real slabs and boards at working size — specification happens by touch.'], 'extra' => ['tag' => ['en' => 'Material Wall']]],
                    ['label' => ['en' => 'In-house production'], 'value' => ['en' => 'Machining, veneering, spraying and pre-assembly under one roof.'], 'extra' => ['tag' => ['en' => 'Factory']]],
                    ['label' => ['en' => 'Our own fitters'], 'value' => ['en' => 'The team that drew the detail is the team that fits it.'], 'extra' => ['tag' => ['en' => 'Installation']]],
                ],
            ],
            [
                'key' => 'contact_rows',
                'name' => 'Contact details',
                'description' => 'Your phone, email, address and opening hours. These appear on the contact page and in the footer — edit them here.',
                'schema' => ['label' => 'Label', 'value' => 'Value'],
                'items' => [
                    ['key' => 'showroom', 'label' => ['en' => 'Showroom'], 'value' => ['en' => 'Novera Interiors, 100m Road, Erbil, Kurdistan Region, Iraq']],
                    ['key' => 'phone', 'label' => ['en' => 'Phone'], 'value' => ['en' => '+964 750 000 0000'], 'url' => 'tel:+9647500000000'],
                    ['key' => 'email', 'label' => ['en' => 'Email'], 'value' => ['en' => 'info@noverainteriors.com'], 'url' => 'mailto:info@noverainteriors.com'],
                    ['key' => 'hours', 'label' => ['en' => 'Hours'], 'value' => ['en' => 'Saturday – Thursday, 10:00 – 19:00']],
                ],
            ],
            [
                'key' => 'socials',
                'name' => 'Social links',
                'description' => 'The social profiles linked from the footer.',
                'schema' => ['label' => 'Network', 'url' => 'Profile URL'],
                'items' => [
                    ['label' => ['en' => 'Instagram'], 'url' => '#'],
                    ['label' => ['en' => 'Facebook'], 'url' => '#'],
                    ['label' => ['en' => 'Pinterest'], 'url' => '#'],
                    ['label' => ['en' => 'LinkedIn'], 'url' => '#'],
                ],
            ],
            [
                'key' => 'project_filters',
                'name' => 'Project filters',
                'description' => 'The buttons above the portfolio that narrow it down. Adding one needs the two extra details — ask a developer if you are unsure.',
                'schema' => ['label' => 'Label', 'extra.type' => 'all|status|category|concept', 'extra.match' => 'Key to match'],
                'items' => [
                    ['label' => ['en' => 'All'], 'extra' => ['type' => 'all']],
                    ['label' => ['en' => 'Completed'], 'extra' => ['type' => 'status', 'match' => 'completed']],
                    ['label' => ['en' => 'Ongoing'], 'extra' => ['type' => 'status', 'match' => 'ongoing']],
                    ['label' => ['en' => 'Residential'], 'extra' => ['type' => 'category', 'match' => 'residential']],
                    ['label' => ['en' => 'Commercial'], 'extra' => ['type' => 'category', 'match' => 'commercial']],
                    ['label' => ['en' => 'Kitchen'], 'extra' => ['type' => 'concept', 'match' => 'kitchen']],
                    ['label' => ['en' => 'Bedroom'], 'extra' => ['type' => 'concept', 'match' => 'bedroom']],
                    ['label' => ['en' => 'Bathroom'], 'extra' => ['type' => 'concept', 'match' => 'bathroom']],
                ],
            ],
            [
                'key' => 'swatches',
                'name' => 'Brand palette',
                'description' => 'The brand colours, listed on the design-system page.',
                'schema' => ['label' => 'Name', 'value' => 'Usage', 'extra.hex' => 'Hex'],
                'items' => [
                    ['label' => ['en' => 'Novera Navy'], 'value' => ['en' => 'Typography, duotone ground, major sections'], 'extra' => ['hex' => '#131936']],
                    ['label' => ['en' => 'Ink'], 'value' => ['en' => 'Brand strip, footer, immersive grounds'], 'extra' => ['hex' => '#0B0F22']],
                    ['label' => ['en' => 'Light Blue'], 'value' => ['en' => 'Alternating surfaces, outlined numerals'], 'extra' => ['hex' => '#EEF3F9']],
                    ['label' => ['en' => 'Luxury Gold'], 'value' => ['en' => 'Hairlines, italic accents, active states only'], 'extra' => ['hex' => '#F2D9A0']],
                    ['label' => ['en' => 'White'], 'value' => ['en' => 'Primary editorial background'], 'extra' => ['hex' => '#FFFFFF']],
                ],
            ],
            [
                'key' => 'motion_notes',
                'name' => 'Animation direction',
                'schema' => ['label' => 'Stage', 'value' => 'Behaviour'],
                'items' => [
                    ['label' => ['en' => 'Page Load'], 'value' => ['en' => 'Hero video runs a 34s slow scale; headline rises 46px on a 1.6s ease-out curve; the eyebrow fades over 1.4s.']],
                    ['label' => ['en' => 'Scroll'], 'value' => ['en' => 'Blocks below the fold rise 44px once 5% visible. Full-bleed imagery parallaxes at 0.035–0.05. The concept index marquees at 46s per cycle.']],
                    ['label' => ['en' => 'Hover'], 'value' => ['en' => 'Duotone resolves to full colour, images scale 1.05–1.09 over 1.5s, a gold hairline fades in at 0.7s, arrows travel 6px.']],
                    ['label' => ['en' => 'Restraint'], 'value' => ['en' => 'One motion per element. No bounce, no stagger past 200ms, nothing faster than 400ms.']],
                ],
            ],
        ];
    }
}

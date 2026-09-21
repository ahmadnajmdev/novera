<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Seeder;

/**
 * Builds every public page out of ordered, typed blocks.
 *
 * Headings use a "*accent*" marker for the gold italic fragment. The renderer
 * splits on it and translates each fragment separately, which is exactly how
 * the design's dictionary is keyed.
 */
class PageSeeder extends Seeder
{
    protected array $media = [];

    public function run(): void
    {
        $this->media = Media::where('folder', 'placeholders')->pluck('id', 'filename')->all();

        foreach ($this->pages() as $sort => $definition) {
            $sections = $definition['sections'];
            unset($definition['sections']);

            $page = Page::updateOrCreate(
                ['key' => $definition['key']],
                array_merge($definition, [
                    'published_at' => now(),
                    'sort' => $sort,
                ]),
            );

            $page->sections()->delete();

            foreach ($sections as $index => $section) {
                Section::create([
                    'page_id' => $page->id,
                    'type' => $section['type'],
                    'data' => $section['data'] ?? [],
                    'settings' => $section['settings'] ?? [],
                    'is_visible' => true,
                    'sort' => $index,
                ]);
            }
        }
    }

    protected function img(string $key): ?int
    {
        return $this->media[$key] ?? null;
    }

    protected function pages(): array
    {
        return [
            [
                'key' => 'home',
                'slug' => ['en' => ''],
                'title' => ['en' => 'Home'],
                'template' => 'builder',
                'is_system' => true,
                'dark_hero' => true,
                'seo_title' => ['en' => 'Novera Interiors — Crafting spaces that define living'],
                'sections' => [
                    [
                        'type' => 'hero.video',
                        'data' => [
                            'eyebrow' => ['en' => 'Interior Design · Materials · Manufacturing · Installation'],
                            'heading' => ['en' => 'Crafting spaces that define *living*'],
                            'lead' => ['en' => 'One studio draws it, one workshop makes it, one team installs it. Nothing is subcontracted, so nothing is compromised.'],
                            'primary_label' => ['en' => 'Explore Our Work'],
                            'primary_page' => 'projects',
                            'secondary_label' => ['en' => 'Discover Novera'],
                            'secondary_page' => 'about',
                        ],
                        'settings' => ['marquee' => true, 'scroll_hint' => true, 'source' => 'settings'],
                    ],
                    [
                        'type' => 'intro.split',
                        'data' => [
                            'number' => '01',
                            'heading' => ['en' => 'We create more than interiors. We create *experiences*.'],
                            'body' => ['en' => 'Every Novera space begins as a conversation about how a room will actually be used. It becomes a drawing, then a material selection, then a workshop drawing, then a finished piece — without ever leaving our hands.'],
                            'caption' => ['en' => 'Ashti Penthouse — the media wall was set out from a single reserved onyx block.'],
                            'media_id' => $this->img('fireplaceDark'),
                        ],
                        'settings' => ['collection' => 'stats', 'background' => 'white'],
                    ],
                    [
                        'type' => 'projects.featured',
                        'data' => [
                            'number' => '02',
                            'heading' => ['en' => 'Selected *work*'],
                            'link_label' => ['en' => 'All Nine Projects'],
                            'link_page' => 'projects',
                        ],
                        'settings' => ['background' => 'ink', 'limit' => 3],
                    ],
                    [
                        'type' => 'concepts.rail',
                        'data' => [
                            'number' => '03',
                            'heading' => ['en' => 'Ten rooms, one standard of *execution*'],
                            'body' => ['en' => 'Each concept is documented as ideas, styles, interior finishes and accessories, so a decision made in the showroom is the decision that reaches the site.'],
                            'hint' => ['en' => 'Hover to pause'],
                            'cta_label' => ['en' => 'All Ten Concepts'],
                            'cta_page' => 'concepts',
                        ],
                        'settings' => ['background' => 'white'],
                    ],
                    [
                        'type' => 'materials.index',
                        'data' => [
                            'number' => '04',
                            'heading' => ['en' => 'A material *library*, not a catalogue'],
                            'body' => ['en' => 'Fourteen families, sourced and finished in-house. Hover a name to see it.'],
                            'cta_label' => ['en' => 'Enter Library'],
                            'cta_page' => 'materials',
                        ],
                        'settings' => ['background' => 'navy'],
                    ],
                    [
                        'type' => 'showroom.feature',
                        'data' => [
                            'number' => '05',
                            'heading' => ['en' => 'Showroom & *factory*'],
                            'wide_media_id' => $this->img('kitchenWide'),
                            'card_eyebrow' => ['en' => 'The Showroom'],
                            'card_body' => ['en' => 'Full-scale rooms, real slabs, working hardware. Clients specify by touching the material, not by reading a code.'],
                            'cards' => [
                                [
                                    'title' => ['en' => 'The Factory'],
                                    'body' => ['en' => 'Machined to the millimetre, finished by hand. Manufacturing in-house means we own the tolerance and the timeline.'],
                                    'media_id' => $this->img('kitchenSink'),
                                ],
                                [
                                    'title' => ['en' => 'The Craft'],
                                    'body' => ['en' => 'The same team that draws the detail installs it. Nothing is subcontracted, so nothing is compromised.'],
                                    'media_id' => $this->img('pillows'),
                                ],
                            ],
                        ],
                        'settings' => ['background' => 'light'],
                    ],
                    [
                        'type' => 'services.compact',
                        'data' => [
                            'number' => '06',
                            'heading' => ['en' => 'Four stages, one continuous *responsibility*'],
                        ],
                        'settings' => ['background' => 'white'],
                    ],
                    [
                        'type' => 'cta.banner',
                        'data' => [
                            'heading' => ['en' => "Let's create something *extraordinary*."],
                            'button_label' => ['en' => 'Start Your Project'],
                            'button_page' => 'contact',
                            'media_id' => $this->img('chaise'),
                        ],
                        'settings' => [],
                    ],
                ],
            ],

            [
                'key' => 'about',
                'slug' => ['en' => 'about'],
                'title' => ['en' => 'About Us'],
                'dark_hero' => true,
                'sections' => [
                    [
                        'type' => 'hero.image',
                        'data' => [
                            'eyebrow' => ['en' => 'About Us'],
                            'heading' => ['en' => 'A studio, a workshop, a *standard*'],
                            'media_id' => $this->img('hallway'),
                        ],
                        'settings' => ['height' => 'min(78vh,760px)'],
                    ],
                    [
                        'type' => 'about.story',
                        'data' => [
                            'eyebrow' => ['en' => 'Brand Story'],
                            'heading' => ['en' => 'We began with a workshop, not a portfolio'],
                            'body_one' => ['en' => "Novera Interiors grew out of a manufacturing floor. Long before the studio existed, the craft did — cutting, veneering, spraying, fitting. When the design practice was added, it inherited a workshop's intolerance for approximation."],
                            'body_two' => ['en' => 'Today Novera works across private residences, penthouses, hospitality and commercial interiors: a single team that draws the space, selects the stone, machines the joinery and hands over the finished room.'],
                            'standards_eyebrow' => ['en' => 'Our Standards'],
                        ],
                        'settings' => ['collection' => 'standards'],
                    ],
                    [
                        'type' => 'about.facilities',
                        'data' => ['media_id' => $this->img('lobbyWood')],
                        'settings' => ['collection' => 'facilities'],
                    ],
                ],
            ],

            [
                'key' => 'concepts',
                'slug' => ['en' => 'concepts'],
                'title' => ['en' => 'Concepts'],
                'dark_hero' => true,
                'sections' => [
                    [
                        'type' => 'hero.plain',
                        'data' => [
                            'eyebrow' => ['en' => 'Concepts'],
                            'heading' => ['en' => 'Every room, documented to the *accessory*'],
                        ],
                        'settings' => ['background' => 'ink'],
                    ],
                    ['type' => 'concepts.grid', 'data' => [], 'settings' => ['background' => 'ink']],
                ],
            ],

            [
                'key' => 'projects',
                'slug' => ['en' => 'projects'],
                'title' => ['en' => 'Our Projects'],
                'sections' => [
                    [
                        'type' => 'hero.plain',
                        'data' => [
                            'eyebrow' => ['en' => 'Our Projects'],
                            'heading' => ['en' => 'The work, in order of *delivery*'],
                        ],
                        'settings' => ['background' => 'white'],
                    ],
                    ['type' => 'projects.grid', 'data' => [], 'settings' => ['filters' => true]],
                ],
            ],

            [
                'key' => 'materials',
                'slug' => ['en' => 'materials'],
                'title' => ['en' => 'Our Materials'],
                'dark_hero' => true,
                'sections' => [
                    [
                        'type' => 'hero.plain',
                        'data' => [
                            'eyebrow' => ['en' => 'Our Materials'],
                            'heading' => ['en' => 'The Novera material *library*'],
                            'intro' => ['en' => 'Two families, fourteen materials. Terminology follows the workshop, not the brochure.'],
                        ],
                        'settings' => ['background' => 'ink'],
                    ],
                    ['type' => 'materials.groups', 'data' => [], 'settings' => []],
                ],
            ],

            [
                'key' => 'services',
                'slug' => ['en' => 'services'],
                'title' => ['en' => 'Services'],
                'sections' => [
                    [
                        'type' => 'hero.plain',
                        'data' => [
                            'eyebrow' => ['en' => 'Services'],
                            'heading' => ['en' => 'From first sketch to final *fixing*'],
                            'intro' => ['en' => 'One contract, one team, one point of accountability. The four stages are sequential, but the same people carry the project through all of them.'],
                        ],
                        'settings' => ['background' => 'white'],
                    ],
                    ['type' => 'services.full', 'data' => [], 'settings' => []],
                ],
            ],

            [
                'key' => 'contact',
                'slug' => ['en' => 'contact'],
                'title' => ['en' => 'Contact Us'],
                'dark_hero' => true,
                'sections' => [
                    [
                        'type' => 'hero.plain',
                        'data' => [
                            'eyebrow' => ['en' => 'Contact Us'],
                            'heading' => ['en' => "Let's create something *extraordinary*."],
                        ],
                        'settings' => ['background' => 'ink'],
                    ],
                    [
                        'type' => 'contact.form',
                        'data' => [
                            'eyebrow' => ['en' => 'Start Your Project'],
                            'media_id' => $this->img('livingSet'),
                        ],
                        'settings' => ['form' => 'enquiry', 'collection' => 'contact_rows'],
                    ],
                ],
            ],
        ];
    }
}

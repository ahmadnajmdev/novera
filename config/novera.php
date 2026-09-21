<?php

return [
    /*
     | When enabled, any string rendered through the site translator that has
     | no dictionary entry is recorded so it shows up in the CMS translation
     | screen. Leave off in production; it writes on render.
     */
    'collect_strings' => env('NOVERA_COLLECT_STRINGS', false),

    /*
     | Query string that puts the front end into visual-edit mode. The value is
     | a signed token issued by the CMS, never a plain boolean.
     */
    'edit_param' => 'nv-edit',

    /*
     | Index pages that own a detail route. The key is the page key, the value
     | the model whose translated slug forms the second URL segment.
     */
    'detail_pages' => [
        'concepts' => App\Models\Concept::class,
        'projects' => App\Models\Project::class,
        'materials' => App\Models\Material::class,
    ],

    'reserved_prefixes' => ['admin', 'livewire', 'storage', 'up', 'build', 'fonts'],

    'image' => [
        'placeholder_host' => 'https://images.unsplash.com/photo-',
    ],
];

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ContentItem extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label', 'value'];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'value' => 'array',
            'extra' => 'array',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ContentCollection::class, 'content_collection_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}

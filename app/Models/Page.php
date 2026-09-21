<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'slug', 'title', 'eyebrow', 'heading', 'intro', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'array',
            'title' => 'array',
            'eyebrow' => 'array',
            'heading' => 'array',
            'intro' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'is_system' => 'boolean',
            'dark_hero' => 'boolean',
            'noindex' => 'boolean',
            'published_at' => 'datetime',
            'sort' => 'integer',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort');
    }

    public function visibleSections(): HasMany
    {
        return $this->sections()->where('is_visible', true);
    }

    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}

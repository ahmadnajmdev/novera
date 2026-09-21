<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'slug', 'name', 'location', 'headline', 'body', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'array',
            'name' => 'array',
            'location' => 'array',
            'headline' => 'array',
            'body' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function wideMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'wide_media_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)->withPivot('sort')->orderBy('pivot_sort');
    }

    public function concepts(): BelongsToMany
    {
        return $this->belongsToMany(Concept::class)->withPivot('sort')->orderBy('pivot_sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}

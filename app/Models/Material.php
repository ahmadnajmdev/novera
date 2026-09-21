<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Material extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['slug', 'name', 'tag', 'blurb', 'body', 'seo_title', 'seo_description'];

    protected function casts(): array
    {
        return [
            'slug' => 'array',
            'name' => 'array',
            'tag' => 'array',
            'blurb' => 'array',
            'body' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class, 'material_group_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(MaterialSpec::class)->orderBy('sort');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MaterialImage::class)->orderBy('sort');
    }

    public function concepts(): BelongsToMany
    {
        return $this->belongsToMany(Concept::class)->withPivot('sort')->orderBy('pivot_sort');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withPivot('sort')->orderBy('pivot_sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}

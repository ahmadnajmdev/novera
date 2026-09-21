<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name', 'body'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'body' => 'array',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(ServicePoint::class)->orderBy('sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}

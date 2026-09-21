<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ConceptTab extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label', 'heading_template'];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'heading_template' => 'array',
            'is_visible' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('sort');
    }
}

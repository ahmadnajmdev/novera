<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label'];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'is_visible' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort');
    }

    public function href(?string $locale = null): string
    {
        if ($this->page) {
            return app(\App\Support\Urls::class)->page($this->page, $locale);
        }

        return $this->url ?: '#';
    }
}

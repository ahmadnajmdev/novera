<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentCollection extends Model
{
    use HasAutoKey;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['schema' => 'array'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class)->orderBy('sort');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }
}

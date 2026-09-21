<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MaterialGroup extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return ['name' => 'array', 'sort' => 'integer'];
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class)->where('is_active', true)->orderBy('sort');
    }
}

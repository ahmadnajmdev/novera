<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class MaterialSpec extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label', 'value'];

    protected function casts(): array
    {
        return ['label' => 'array', 'value' => 'array', 'sort' => 'integer'];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}

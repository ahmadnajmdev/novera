<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ServicePoint extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['text'];

    protected function casts(): array
    {
        return ['text' => 'array', 'sort' => 'integer'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}

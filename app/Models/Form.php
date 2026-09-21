<?php

namespace App\Models;

use App\Models\Concerns\HasAutoKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasAutoKey;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['submit_label', 'success_message'];

    protected function casts(): array
    {
        return [
            'submit_label' => 'array',
            'success_message' => 'array',
            'store_submissions' => 'boolean',
        ];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->where('is_visible', true)->orderBy('sort');
    }

    public function allFields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class)->latest();
    }
}

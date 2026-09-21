<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class FormField extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['label', 'placeholder'];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'placeholder' => 'array',
            'options' => 'array',
            'is_required' => 'boolean',
            'is_visible' => 'boolean',
            'rows' => 'integer',
            'sort' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Select options are stored as [['value' => 'x', 'label' => ['en' => 'X']]].
     */
    public function optionList(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return collect($this->options ?? [])
            ->mapWithKeys(function (array $option) use ($locale) {
                $label = $option['label'] ?? [];
                $text = is_array($label)
                    ? ($label[$locale] ?? $label[config('app.fallback_locale')] ?? reset($label))
                    : $label;

                return [$option['value'] ?? $text => $text];
            })
            ->all();
    }

    public function validationRules(): array
    {
        $rules = $this->is_required ? ['required'] : ['nullable'];

        if ($this->type === 'email') {
            $rules[] = 'email';
        }

        $rules[] = 'string';
        $rules[] = 'max:5000';

        if (filled($this->rules)) {
            $rules = array_merge($rules, explode('|', $this->rules));
        }

        return array_values(array_unique($rules));
    }
}

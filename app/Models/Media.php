<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Media extends Model
{
    use HasTranslations;

    protected $table = 'media';

    protected $guarded = [];

    public array $translatable = ['alt', 'caption'];

    protected function casts(): array
    {
        return [
            'alt' => 'array',
            'caption' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * Media may be an uploaded file or a remote URL, so callers can always
     * ask for url() without caring which of the two this row is.
     */
    public function url(): string
    {
        if (filled($this->external_url)) {
            return $this->external_url;
        }

        if (blank($this->path)) {
            return '';
        }

        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }

    public function getUrlAttribute(): string
    {
        return $this->url();
    }
}

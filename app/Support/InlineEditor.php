<?php

namespace App\Support;

use App\Models\Concept;
use App\Models\ConceptItem;
use App\Models\ContentItem;
use App\Models\Material;
use App\Models\MaterialGroup;
use App\Models\MaterialSpec;
use App\Models\Media;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Project;
use App\Models\Revision;
use App\Models\Section;
use App\Models\Service;
use App\Models\ServicePoint;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Applies edits made in the visual editor.
 *
 * Only the models and fields listed here can be written, so a crafted message
 * from the preview frame cannot reach arbitrary columns.
 */
class InlineEditor
{
    protected const MODELS = [
        'section' => Section::class,
        'page' => Page::class,
        'concept' => Concept::class,
        'concept_item' => ConceptItem::class,
        'project' => Project::class,
        'material' => Material::class,
        'material_group' => MaterialGroup::class,
        'material_spec' => MaterialSpec::class,
        'service' => Service::class,
        'service_point' => ServicePoint::class,
        'content_item' => ContentItem::class,
        'menu_item' => MenuItem::class,
    ];

    /**
     * Picture slots a section may expose. Separate from the text allowlist
     * because these hold a media id, not translated wording.
     */
    protected const SECTION_MEDIA_FIELDS = ['media_id', 'wide_media_id'];

    /** Fields a section block may expose; anything else is rejected. */
    protected const SECTION_FIELDS = [
        'eyebrow', 'heading', 'lead', 'intro', 'body', 'body_one', 'body_two',
        'caption', 'number', 'hint', 'link_label', 'cta_label', 'button_label',
        'primary_label', 'secondary_label', 'card_eyebrow', 'card_body',
        'standards_eyebrow',
    ];

    public function resolve(string $model, int|string $id): Model
    {
        $class = self::MODELS[$model] ?? throw new InvalidArgumentException("Unknown model [{$model}].");

        return $class::findOrFail($id);
    }

    public function read(string $model, int|string $id, string $field, string $locale): ?string
    {
        $record = $this->resolve($model, $id);

        if ($record instanceof Section) {
            $this->assertSectionField($field);

            return data_get($record->data, "{$field}.{$locale}");
        }

        $this->assertTranslatable($record, $field);

        return $record->getTranslations($field)[$locale] ?? null;
    }

    public function write(string $model, int|string $id, string $field, string $locale, ?string $value): Model
    {
        $record = $this->resolve($model, $id);
        $before = $this->read($model, $id, $field, $locale);

        if ($record instanceof Section) {
            $this->assertSectionField($field);

            $data = $record->data ?? [];
            $existing = $data[$field] ?? [];
            $data[$field] = array_merge(is_array($existing) ? $existing : [], [$locale => $value]);
            $record->data = $data;
        } else {
            $this->assertTranslatable($record, $field);
            $record->setTranslation($field, $locale, (string) $value);
        }

        $record->save();

        Revision::create([
            'revisionable_type' => $record::class,
            'revisionable_id' => $record->getKey(),
            'user_id' => auth()->id(),
            'before' => [$field => [$locale => $before]],
            'after' => [$field => [$locale => $value]],
            'source' => 'visual-editor',
        ]);

        return $record;
    }

    /** The media currently in a section's picture slot. */
    public function readMedia(string $model, int|string $id, string $field): ?int
    {
        $record = $this->resolve($model, $id);

        if (! $record instanceof Section) {
            throw new InvalidArgumentException('Only sections carry pictures.');
        }

        $this->assertSectionMediaField($field);

        $value = data_get($record->data, $field);

        return $value === null ? null : (int) $value;
    }

    /**
     * Swap the picture in a section's slot. Null clears it, which is how the
     * editor removes a background without deleting the section.
     */
    public function writeMedia(string $model, int|string $id, string $field, ?int $mediaId): Section
    {
        $record = $this->resolve($model, $id);

        if (! $record instanceof Section) {
            throw new InvalidArgumentException('Only sections carry pictures.');
        }

        $this->assertSectionMediaField($field);

        if ($mediaId !== null && ! Media::whereKey($mediaId)->exists()) {
            throw new InvalidArgumentException('That picture is not in the library.');
        }

        $before = data_get($record->data, $field);

        $data = $record->data ?? [];
        $data[$field] = $mediaId;
        $record->data = $data;
        $record->save();

        Revision::create([
            'revisionable_type' => $record::class,
            'revisionable_id' => $record->getKey(),
            'user_id' => auth()->id(),
            'before' => [$field => $before],
            'after' => [$field => $mediaId],
            'source' => 'visual-editor',
        ]);

        return $record;
    }

    protected function assertSectionMediaField(string $field): void
    {
        if (! in_array($field, self::SECTION_MEDIA_FIELDS, true)) {
            throw new InvalidArgumentException("Picture slot [{$field}] is not editable.");
        }
    }

    protected function assertSectionField(string $field): void
    {
        if (! in_array($field, self::SECTION_FIELDS, true)) {
            throw new InvalidArgumentException("Field [{$field}] is not editable on a block.");
        }
    }

    protected function assertTranslatable(Model $record, string $field): void
    {
        $translatable = property_exists($record, 'translatable') ? $record->translatable : [];

        if (! in_array($field, $translatable, true)) {
            throw new InvalidArgumentException("Field [{$field}] is not editable on ".class_basename($record).'.');
        }
    }

    /** Render the stored value the way the theme will show it. */
    public function preview(string $field, ?string $value, string $locale): string
    {
        return in_array($field, ['heading'], true)
            ? nv_accent($value, $locale)
            : e(nv_t($value, $locale));
    }
}

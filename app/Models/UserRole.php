<?php

namespace App\Models;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Editor => 'Editor',
        };
    }

    /**
     * Editors work on content. Everything that changes how the site is wired —
     * languages, users, redirects, forms, settings — stays with administrators.
     */
    public function canManageConfiguration(): bool
    {
        return $this === self::Admin;
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $role) => [$role->value => $role->label()])->all();
    }
}

<?php

namespace App\Filament\Forms;

use Closure;
use Filament\Forms\Components\Field;

/**
 * A choice made by looking at it.
 *
 * A dropdown of seventeen names asks the editor to already know what
 * "hero.video" or "intro.split" looks like. This renders each choice as a
 * card with a name, a plain-language description and an icon, grouped under
 * headings, so the decision is made by reading rather than by recall.
 */
class CardPicker extends Field
{
    protected string $view = 'filament.forms.card-picker';

    /** @var array<string, array{label: string, hint?: string, icon?: string, group?: string}>|Closure */
    protected array|Closure $cards = [];

    protected int|Closure $cardColumns = 2;

    /** @param  array<string, array<string, mixed>>|Closure  $cards */
    public function cards(array|Closure $cards): static
    {
        $this->cards = $cards;

        return $this;
    }

    public function cardColumns(int|Closure $columns): static
    {
        $this->cardColumns = $columns;

        return $this;
    }

    /** @return array<string, array<string, mixed>> */
    public function getCards(): array
    {
        return $this->evaluate($this->cards);
    }

    public function getCardColumns(): int
    {
        return $this->evaluate($this->cardColumns);
    }

    /**
     * Cards in display order, bucketed by their group heading. Ungrouped
     * cards land under an empty key, which the view renders without a
     * heading.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function getGroupedCards(): array
    {
        $groups = [];

        foreach ($this->getCards() as $value => $card) {
            $groups[$card['group'] ?? ''][$value] = $card;
        }

        return $groups;
    }
}

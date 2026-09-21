@php
    $statePath = $getStatePath();
    $selected = $getState();
    $groups = $getGroupedCards();
    $columns = $getCardColumns();
@endphp

<div class="nv-cards" style="--nv-cards-columns: {{ $columns }}">
    @foreach ($groups as $group => $cards)
        @if (filled($group))
            <p class="nv-cards__group">{{ $group }}</p>
        @endif

        <div class="nv-cards__grid">
            @foreach ($cards as $value => $card)
                <button
                    type="button"
                    wire:key="{{ $statePath }}-{{ $value }}"
                    wire:click="$set(@js($statePath), @js($value))"
                    wire:loading.attr="disabled"
                    @class(['nv-card', 'nv-card--on' => $selected === $value])
                    aria-pressed="{{ $selected === $value ? 'true' : 'false' }}"
                >
                    @if (! empty($card['icon']))
                        <span class="nv-card__icon">
                            <x-filament::icon :icon="$card['icon']" class="h-5 w-5" />
                        </span>
                    @endif

                    <span class="nv-card__text">
                        <span class="nv-card__label">{{ $card['label'] }}</span>
                        @if (! empty($card['hint']))
                            <span class="nv-card__hint">{{ $card['hint'] }}</span>
                        @endif
                    </span>

                    <span class="nv-card__tick" aria-hidden="true">
                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5" />
                    </span>
                </button>
            @endforeach
        </div>
    @endforeach
</div>

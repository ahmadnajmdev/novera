<x-filament-widgets::widget>
    <div class="nv-start">
        <div class="nv-start__intro">
            <h2 class="nv-start__greeting">{{ $this->getGreeting() }}</h2>
            <p class="nv-start__lead">
                Everything on the website is edited from here. Pick whichever of these
                you came to do — nothing you click is permanent until you press save.
            </p>
        </div>

        <div class="nv-start__grid">
            @foreach ($this->getCards() as $card)
                <a href="{{ $card['url'] }}"
                   class="nv-start__card {{ ($card['primary'] ?? false) ? 'nv-start__card--primary' : '' }}">
                    <span class="nv-start__icon">
                        <x-filament::icon :icon="$card['icon']" class="h-6 w-6" />
                    </span>

                    <span class="nv-start__body">
                        <span class="nv-start__title">
                            {{ $card['title'] }}
                            @if (! empty($card['badge']))
                                <x-filament::badge color="danger" size="xs">{{ $card['badge'] }}</x-filament::badge>
                            @endif
                        </span>
                        <span class="nv-start__text">{{ $card['body'] }}</span>
                        <span class="nv-start__action">
                            {{ $card['action'] }}
                            <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    
</x-filament-widgets::widget>

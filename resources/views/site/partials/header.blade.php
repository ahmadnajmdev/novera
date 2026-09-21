@php
    $site = nv_site();
    $primary = $site->menu('primary');
    $indexMenu = $site->menu('index');
    $locales = $site->locales();
    $activePageId = ($page ?? null)?->id;
    $navSolid = ! ($darkHero ?? false);
@endphp

<header class="nv-header"
        x-data="{ open: false, hovered: 0 }"
        @keydown.escape.window="open = false">

    <div class="nv-brandstrip">
        <div class="nv-brandstrip__inner">
            <a href="{{ nv_url('home') }}">
                <img src="{{ nv_media(nv_setting('brand.logo_gold')) }}" alt="{{ nv_setting('brand.name') }}">
            </a>
        </div>
    </div>

    <div class="nv-nav {{ $navSolid ? 'is-solid' : '' }}"
         data-nav
         data-nav-solid="{{ $navSolid ? '1' : '0' }}"
         :style="'background:' + ($el.classList.contains('is-solid') ? 'rgba(11,15,34,.92)' : 'transparent')"
         style="background: {{ $navSolid ? 'rgba(11,15,34,.92)' : 'transparent' }};
                backdrop-filter: {{ $navSolid ? 'saturate(140%) blur(18px)' : 'none' }};
                border-bottom: 1px solid {{ $navSolid ? 'rgba(242,217,160,.2)' : 'rgba(255,255,255,.14)' }};">
        <div class="nv-container nv-nav__inner">
            <a href="{{ nv_url('home') }}" class="nv-nav__logo" style="flex-shrink:0">
                <img src="{{ nv_media(nv_setting('brand.logo_navy')) }}" alt="{{ nv_setting('brand.name') }}">
            </a>

            <nav class="nv-nav__links">
                <span class="nv-nav__links" style="display:none" x-show="window.innerWidth >= 1080" x-init="$el.style.display = window.innerWidth >= 1080 ? 'flex' : 'none'">
                    @foreach ($primary as $item)
                        @php $isActive = $item->page_id && $item->page_id === $activePageId; @endphp
                        <a href="{{ $item->href($locale) }}" class="nv-nav__link {{ $isActive ? 'is-active' : '' }}">
                            @if ($isActive)<span class="nv-nav__dot"></span>@endif
                            {{ nv_tr($item, 'label') }}
                        </a>
                    @endforeach
                    <a href="{{ nv_url('contact') }}" class="nv-nav__cta">@t('Contact')</a>
                </span>

                <span class="nv-langs">
                    @foreach ($locales as $item)
                        <a href="{{ nv_alternate_url($item->code) }}"
                           class="nv-lang {{ $item->code === $locale ? 'is-active' : '' }}"
                           lang="{{ $item->code }}"
                           hreflang="{{ $item->code }}">{{ $item->native_name }}</a>
                    @endforeach
                </span>

                <button type="button" class="nv-menu-toggle" @click="open = true" aria-haspopup="dialog" :aria-expanded="open">
                    @t('Index')
                    <span class="nv-menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
                </button>
            </nav>
        </div>
    </div>

    {{-- Index overlay --}}
    <div class="nv-overlay" x-show="open" x-cloak role="dialog" aria-modal="true" x-transition.opacity>
        <div class="nv-overlay__top">
            <img src="{{ nv_media(nv_setting('brand.logo_gold')) }}" alt="{{ nv_setting('brand.name') }}">
            <span style="display:flex;align-items:center;gap:22px">
                <span class="nv-langs" style="border:0;padding-inline-end:22px;border-inline-end:1px solid rgba(255,255,255,.18)">
                    @foreach ($locales as $item)
                        <a href="{{ nv_alternate_url($item->code) }}" class="nv-lang {{ $item->code === $locale ? 'is-active' : '' }}" lang="{{ $item->code }}">{{ $item->native_name }}</a>
                    @endforeach
                </span>
                <button type="button" class="nv-menu-toggle" style="color:var(--nv-gold)" @click="open = false">@t('Close')</button>
            </span>
        </div>

        <div class="nv-overlay__body">
            <div class="nv-overlay__links">
                @foreach ($indexMenu as $index => $item)
                    <a href="{{ $item->href($locale) }}"
                       class="nv-overlay__link {{ $item->page_id === $activePageId ? 'is-active' : '' }}"
                       @mouseenter="hovered = {{ $index }}">
                        <span class="nv-overlay__num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="nv-overlay__label">{{ nv_tr($item, 'label') }}</span>
                    </a>
                @endforeach
            </div>

            @php $overlayImages = \App\Models\Concept::active()->with('media')->take(7)->get(); @endphp
            <div class="nv-overlay__aside">
                @foreach ($overlayImages as $index => $concept)
                    <img x-show="hovered % {{ max($overlayImages->count(), 1) }} === {{ $index }}"
                         x-cloak
                         loading="lazy" decoding="async"
                         src="{{ nv_img($concept->media, 1200, 1500) }}" alt="">
                @endforeach
                <div class="nv-overlay__caption">
                    <div class="nv-eyebrow nv-text-gold">@t(nv_setting('brand.legal'))</div>
                    <div style="font:400 16px/1.6 var(--nv-font);color:rgba(255,255,255,.62);margin-top:12px;max-width:34ch">
                        @t(nv_setting('contact.menu_line'))
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('head')
    <style>[x-cloak] { display: none !important; }</style>
@endpush

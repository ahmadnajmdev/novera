@php
    $site = nv_site();
    $tokens = $site->tokens();
    $motion = $site->motion();
    $locales = $site->locales();
    $current = $site->locale($locale);
    $isRtl = $site->isRtl($locale);
    $page = $page ?? null;
    $darkHero = $darkHero ?? ($page?->dark_hero ?? false);
    // Edit mode only changes rendering. Writes never come through the public
    // site — the editor panel saves via the authenticated admin panel — so an
    // auth check plus the flag is the whole gate.
    $editing = auth()->check() && request()->boolean(config('novera.edit_param'));

    // Motion switches are CMS settings; the theme JS reads them at runtime.
    $motionFlags = json_encode([
        'reveal' => $motion['reveal'],
        'parallax' => $motion['parallax'],
        'smoothScroll' => $motion['smooth_scroll'],
        'railAutoscroll' => $motion['rail_autoscroll'],
    ], JSON_THROW_ON_ERROR);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', nv_tr($page, 'seo_title') ?: nv_tr($page, 'title')) — {{ nv_setting('seo.title_suffix', 'Novera Interiors') }}</title>
    <meta name="description" content="@yield('description', nv_tr($page, 'seo_description') ?: nv_setting('seo.default_description'))">
    @if ($page?->noindex)
        <meta name="robots" content="noindex,nofollow">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;700&family=Noto+Naskh+Arabic:wght@400;500;700&display=swap" rel="stylesheet">

    @foreach ($locales as $alternate)
        <link rel="alternate" hreflang="{{ $alternate->code }}" href="{{ nv_alternate_url($alternate->code) }}">
    @endforeach

    {{-- Design tokens are CMS settings, written per request so the palette is
         editable without rebuilding the stylesheet. --}}
    <style>
        :root {
            --nv-ink: {{ $tokens['ink'] }};
            --nv-navy: {{ $tokens['navy'] }};
            --nv-gold: {{ $tokens['gold'] }};
            --nv-gold-deep: {{ $tokens['gold_deep'] }};
            --nv-light: {{ $tokens['light'] }};
            --nv-body: {{ $tokens['body'] }};
            --nv-muted: {{ $tokens['muted'] }};
            --nv-white: {{ $tokens['white'] }};
            --nv-radius: {{ $tokens['radius'] }};
            --nv-max: {{ $tokens['max_width'] }};
            --nv-heading: {{ $isRtl ? $tokens['arabic_heading_font'] : $tokens['heading_font'] }};
            --nv-font: {{ $isRtl ? $tokens['arabic_body_font'] : $tokens['body_font'] }};
        }
    </style>

    <script>
        window.NoveraMotion = {!! $motionFlags !!};
    </script>

    @vite(['resources/css/site.css', 'resources/js/site.js'])
    @if ($editing)
        @vite('resources/js/editor.js')
    @endif
    @livewireStyles
    @stack('head')
</head>
<body @if($isRtl) data-ar="1" @endif class="{{ $editing ? 'nv-editing' : '' }}">

@if ($motion['boot'] && ! $editing)
    <div class="nv-boot" data-boot aria-hidden="true">
        <img src="{{ nv_media(nv_setting('brand.logo_vertical')) }}" alt="{{ nv_setting('brand.name') }}">
        <span></span>
    </div>
@endif

<div class="nv-progress" data-progress aria-hidden="true"><span></span></div>

@include('site.partials.header')

<main>
    @yield('content')
</main>

@include('site.partials.footer')

@livewireScripts
@stack('scripts')
</body>
</html>

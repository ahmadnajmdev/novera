@php
    $motion = nv_site()->motion();
    $concepts = \App\Models\Concept::active()->get();
    $marquee = $concepts->map(fn ($c) => nv_tr($c, 'name'))->all();
    $marquee = array_merge($marquee, ['·'], $marquee);
@endphp

<section class="nv-hero">
    <video data-hero-video autoplay muted loop playsinline
           poster="{{ nv_setting('hero.video_poster') }}">
        <source src="{{ nv_setting('hero.video_url') }}" type="video/mp4">
    </video>
    <div class="nv-hero__scrim"></div>

    <div class="nv-hero__content">
        <div class="nv-container nv-hero__copy">
            <div class="nv-eyebrow nv-text-gold" style="animation:nvIn 1.4s ease .2s both" {!! nv_edit('section', $section->id, 'eyebrow') !!}>
                @t($section->text('eyebrow'))
            </div>

            <h1 class="nv-h1 nv-h1--hero nv-on-dark"
                style="margin:clamp(20px,3vh,38px) 0 0;max-width:16ch;animation:nvUp 1.6s var(--nv-ease) .3s both"
                {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h1>

            <div class="nv-hero__foot">
                <p class="nv-lead" style="color:rgba(255,255,255,.72);max-width:46ch" {!! nv_edit('section', $section->id, 'lead') !!}>
                    @t($section->text('lead'))
                </p>
                <div class="nv-hero__actions">
                    <a href="{{ nv_url($section->text('primary_page') ?: 'projects') }}" class="nv-btn nv-btn--gold">
                        <span {!! nv_edit('section', $section->id, 'primary_label') !!}>@t($section->text('primary_label'))</span>
                    </a>
                    <a href="{{ nv_url($section->text('secondary_page') ?: 'about') }}"
                       class="nv-link-underline" style="color:rgba(255,255,255,.8);border-bottom-color:rgba(242,217,160,.5);padding-bottom:6px">
                        <span {!! nv_edit('section', $section->id, 'secondary_label') !!}>@t($section->text('secondary_label'))</span>
                    </a>
                </div>
            </div>
        </div>

        @if ($section->setting('marquee', true) && $motion['marquee'])
            <div class="nv-marquee" aria-hidden="true">
                <div class="nv-marquee__track">
                    @foreach ($marquee as $word)
                        <span>{{ $word }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if ($section->setting('scroll_hint', true))
        <div class="nv-hero__scroll" aria-hidden="true"><span></span></div>
    @endif
</section>

@extends('site.layout')

@section('title', nv_tr($concept, 'seo_title') ?: nv_tr($concept, 'name'))
@section('description', nv_tr($concept, 'seo_description') ?: nv_tr($concept, 'blurb'))

@php $darkHero = true; @endphp

@section('content')
    <section class="nv-pagehero" style="height:min(76vh,740px);min-height:440px">
        <img loading="lazy" decoding="async" data-parallax="0.05"
             style="position:absolute;inset:-8% 0;width:100%;height:116%;object-fit:cover;animation:nvKen 26s ease-out both"
             src="{{ nv_img($concept->media, 2000, 1200) }}" alt="{{ nv_tr($concept, 'name') }}">
        <div class="nv-pagehero__scrim"></div>
        <div class="nv-pagehero__inner">
            <div class="nv-container nv-pagehero__copy" style="padding-bottom:clamp(40px,7vh,84px)">
                <a href="{{ nv_url('concepts') }}" class="nv-eyebrow" style="color:rgba(255,255,255,.6);letter-spacing:.1em">
                    ← @t('All Concepts')
                </a>
                <div style="display:flex;align-items:baseline;gap:20px;margin-top:22px;flex-wrap:wrap">
                    <span class="nv-eyebrow nv-text-gold" style="letter-spacing:.1em">
                        {{ str_pad((string) ($concept->sort + 1), 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <h1 class="nv-h1 nv-on-dark" style="font-size:clamp(40px,7vw,110px);line-height:1.1;animation:nvUp 1.2s var(--nv-ease) .1s both"
                        {!! nv_edit('concept', $concept->id, 'name') !!}>{{ nv_tr($concept, 'name') }}</h1>
                </div>
                <p class="nv-lead" style="color:rgba(255,255,255,.7);margin:20px 0 0;max-width:50ch"
                   {!! nv_edit('concept', $concept->id, 'blurb') !!}>{{ nv_tr($concept, 'blurb') }}</p>
            </div>
        </div>
    </section>

    <livewire:concept-tabs :concept="$concept" />

    <section class="nv-section--tight nv-bg-light">
        <div class="nv-container nv-pager">
            <a href="{{ nv_entity_url($previous) }}">
                <div class="nv-pager__label">@t('Previous')</div>
                <div class="nv-pager__name">{{ nv_tr($previous, 'name') }}</div>
            </a>
            <a href="{{ nv_entity_url($next) }}" style="text-align:end">
                <div class="nv-pager__label">@t('Next')</div>
                <div class="nv-pager__name">{{ nv_tr($next, 'name') }}</div>
            </a>
        </div>
    </section>
@endsection

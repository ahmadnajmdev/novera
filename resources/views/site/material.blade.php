@extends('site.layout')

@section('title', nv_tr($material, 'seo_title') ?: nv_tr($material, 'name'))
@section('description', nv_tr($material, 'seo_description') ?: nv_tr($material, 'blurb'))

@php $darkHero = true; @endphp

@section('content')
    <section class="nv-pagehero" style="height:min(80vh,800px);min-height:460px">
        <img loading="lazy" decoding="async" data-parallax="0.045"
             style="position:absolute;inset:-8% 0;width:100%;height:116%;object-fit:cover;animation:nvKen 30s ease-out both"
             src="{{ nv_img($material->media, 2000, 1250) }}" alt="{{ nv_tr($material, 'name') }}">
        <div class="nv-pagehero__scrim" style="background:linear-gradient(180deg,rgba(11,15,34,.45),rgba(11,15,34,.88))"></div>
        <div class="nv-pagehero__inner">
            <div class="nv-container nv-pagehero__copy" style="padding-bottom:clamp(40px,7vh,84px)">
                <a href="{{ nv_url('materials') }}" class="nv-eyebrow" style="color:rgba(255,255,255,.6);letter-spacing:.1em">
                    ← @t('Material Library')
                </a>
                <div class="nv-eyebrow nv-text-gold" style="letter-spacing:.15em;margin-top:22px">{{ nv_tr($material->group, 'name') }}</div>
                <h1 class="nv-h1 nv-on-dark" style="font-size:clamp(46px,8vw,126px);line-height:1.08;margin:16px 0 0;animation:nvUp 1.3s var(--nv-ease) .1s both"
                    {!! nv_edit('material', $material->id, 'name') !!}>{{ nv_tr($material, 'name') }}</h1>
            </div>
        </div>
    </section>

    <section class="nv-bg-white" style="padding-block:clamp(60px,9vh,120px)">
        <div class="nv-container" style="display:flex;gap:clamp(34px,5vw,90px);flex-wrap:wrap">
            <div data-reveal class="nv-reveal" style="flex:1 1 400px">
                <p style="font:500 clamp(20px,1.7vw,28px)/1.6 var(--nv-heading);color:var(--nv-navy);margin:0;max-width:42ch"
                   {!! nv_edit('material', $material->id, 'blurb') !!}>{{ nv_tr($material, 'blurb') }}</p>
                <p style="font:400 17px/1.85 var(--nv-font);color:var(--nv-body);margin:26px 0 0;max-width:54ch"
                   {!! nv_edit('material', $material->id, 'body') !!}>{{ nv_tr($material, 'body') }}</p>
            </div>

            <div data-reveal class="nv-reveal" style="flex:1 1 300px">
                @foreach ($material->specs as $spec)
                    <div style="padding:20px 0;border-bottom:1px solid rgba(19,25,54,.14)">
                        <div class="nv-eyebrow nv-eyebrow--sm nv-text-muted" style="letter-spacing:.1em">{{ nv_tr($spec, 'label') }}</div>
                        <div style="font:400 16px/1.7 var(--nv-font);color:var(--nv-navy);margin-top:10px">{{ nv_tr($spec, 'value') }}</div>
                    </div>
                @endforeach

                <a href="{{ nv_url('contact') }}" class="nv-btn nv-btn--outline" style="margin-top:32px">@t('Request A Sample')</a>
            </div>
        </div>
    </section>

    <section class="nv-bg-white" style="padding-block:0 clamp(60px,9vh,120px)">
        <div class="nv-container nv-grid nv-grid--wide" style="gap:clamp(16px,2vw,32px)">
            @foreach ($material->images as $image)
                <div data-reveal class="nv-reveal nv-frame" style="aspect-ratio:1/1">
                    <img loading="lazy" decoding="async" class="nv-frame__zoom"
                         src="{{ nv_img($image->media, 900, 900) }}" alt="">
                </div>
            @endforeach
        </div>
    </section>

    <section class="nv-section--tight nv-bg-light">
        <div class="nv-container">
            <div class="nv-eyebrow nv-text-muted" style="letter-spacing:.15em">@t('Related Concepts')</div>
            <div style="display:flex;flex-wrap:wrap;gap:11px;margin-top:22px">
                @foreach ($material->concepts as $concept)
                    <a href="{{ nv_entity_url($concept) }}" class="nv-pill" style="background:#fff;border-color:transparent;padding:13px 22px">
                        {{ nv_tr($concept, 'name') }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection

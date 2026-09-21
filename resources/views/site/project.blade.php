@extends('site.layout')

@section('title', nv_tr($project, 'seo_title') ?: nv_tr($project, 'name'))
@section('description', nv_tr($project, 'seo_description') ?: nv_tr($project, 'headline'))

@php
    $darkHero = true;
    $meta = [
        ['label' => 'Location', 'value' => nv_tr($project, 'location')],
        ['label' => 'Category', 'value' => nv_tr($project->category, 'name')],
        ['label' => 'Status', 'value' => nv_tr($project->status, 'name')],
        ['label' => 'Year', 'value' => $project->year],
    ];
@endphp

@section('content')
    <section class="nv-pagehero" style="height:min(92vh,940px);min-height:520px">
        <img loading="lazy" decoding="async" data-parallax="0.05"
             style="position:absolute;inset:-8% 0;width:100%;height:116%;object-fit:cover;animation:nvKen 30s ease-out both"
             src="{{ nv_img($project->media, 2200, 1400) }}" alt="{{ nv_tr($project, 'name') }}">
        <div class="nv-pagehero__scrim" style="background:linear-gradient(180deg,rgba(11,15,34,.55) 0%,rgba(11,15,34,.15) 42%,rgba(11,15,34,.92) 100%)"></div>
        <div class="nv-pagehero__inner">
            <div class="nv-container nv-pagehero__copy" style="padding-bottom:clamp(44px,8vh,96px)">
                <a href="{{ nv_url('projects') }}" class="nv-eyebrow" style="color:rgba(255,255,255,.6);letter-spacing:.1em">
                    ← @t('All Projects')
                </a>
                <h1 class="nv-h1 nv-on-dark" style="font-size:clamp(40px,7.2vw,116px);line-height:1.1;margin:22px 0 0;max-width:16ch;animation:nvUp 1.3s var(--nv-ease) .1s both"
                    {!! nv_edit('project', $project->id, 'name') !!}>{{ nv_tr($project, 'name') }}</h1>

                <div style="display:flex;flex-wrap:wrap;gap:clamp(22px,3vw,54px);margin-top:clamp(28px,4vh,44px);padding-top:24px;border-top:1px solid rgba(255,255,255,.2);max-width:880px">
                    @foreach ($meta as $row)
                        <div>
                            <div class="nv-eyebrow" style="font-size:11px;letter-spacing:.1em;color:rgba(255,255,255,.45)">@t($row['label'])</div>
                            <div style="font:400 16px/1.4 var(--nv-font);color:#fff;margin-top:9px">{{ $row['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="nv-bg-white" style="padding-block:clamp(60px,9vh,120px)">
        <div class="nv-container" style="display:flex;gap:clamp(34px,5vw,90px);flex-wrap:wrap">
            <div data-reveal class="nv-reveal" style="flex:1 1 400px">
                <h2 class="nv-h2" style="font-size:clamp(25px,2.8vw,43px);line-height:1.19;max-width:22ch"
                    {!! nv_edit('project', $project->id, 'headline') !!}>{{ nv_tr($project, 'headline') }}</h2>
                <p style="font:400 17px/1.85 var(--nv-font);color:var(--nv-body);margin:24px 0 0;max-width:54ch"
                   {!! nv_edit('project', $project->id, 'body') !!}>{{ nv_tr($project, 'body') }}</p>
            </div>

            <div data-reveal class="nv-reveal" style="flex:1 1 300px">
                <div class="nv-eyebrow nv-eyebrow--sm nv-text-muted" style="letter-spacing:.1em">@t('Materials Used')</div>
                <div style="display:flex;flex-wrap:wrap;gap:9px;margin-top:18px">
                    @foreach ($project->materials as $material)
                        <a href="{{ nv_entity_url($material) }}" class="nv-pill">{{ nv_tr($material, 'name') }}</a>
                    @endforeach
                </div>

                <div class="nv-eyebrow nv-eyebrow--sm nv-text-muted" style="letter-spacing:.1em;margin-top:36px">@t('Concepts Delivered')</div>
                <div style="display:flex;flex-wrap:wrap;gap:9px;margin-top:18px">
                    @foreach ($project->concepts as $concept)
                        <a href="{{ nv_entity_url($concept) }}" class="nv-pill nv-pill--light">{{ nv_tr($concept, 'name') }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="nv-bg-white" style="padding-block:0 clamp(70px,10vh,140px)">
        <div class="nv-container">
            <div data-reveal class="nv-reveal nv-frame" style="aspect-ratio:16/9">
                <img loading="lazy" decoding="async" data-parallax="0.045"
                     style="position:absolute;inset:-10% 0;width:100%;height:120%;object-fit:cover"
                     src="{{ nv_img($project->wideMedia, 2200, 1250) }}" alt="">
            </div>

            <div class="nv-grid nv-grid--wide" style="gap:clamp(16px,2vw,32px);margin-top:clamp(16px,2vw,32px)">
                @foreach ($project->images as $image)
                    <div data-reveal class="nv-reveal nv-frame" style="aspect-ratio:{{ $image->ratio ?: '3/4' }}">
                        <img loading="lazy" decoding="async" class="nv-frame__zoom"
                             src="{{ nv_img($image->media, 900, 1150) }}" alt="">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

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

@php $stats = nv_site()->collection($section->setting('collection', 'stats')); @endphp

<section class="nv-section nv-bg-white" style="padding-block:clamp(80px,12vh,170px) clamp(60px,9vh,120px)">
    <div class="nv-container" style="display:flex;gap:clamp(34px,5vw,96px);flex-wrap:wrap;align-items:flex-start">
        <div data-reveal class="nv-reveal" style="flex:1 1 480px">
            <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start">
                <span class="nv-numeral nv-text-gold-deep" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
                <div>
                    <h2 class="nv-h2" style="font-size:clamp(30px,4.4vw,72px);line-height:1.11;max-width:19ch"
                        {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h2>

                    <p class="nv-lead nv-text-body" style="margin:clamp(24px,3vw,40px) 0 0;max-width:52ch"
                       {!! nv_edit('section', $section->id, 'body') !!}>@t($section->text('body'))</p>

                    <div style="display:flex;flex-wrap:wrap;margin-top:clamp(34px,4vw,58px);border-top:1px solid rgba(19,25,54,.14)">
                        @foreach ($stats as $stat)
                            <div style="flex:1 1 150px;padding:22px 24px 22px 0;border-bottom:1px solid rgba(19,25,54,.14)">
                                <div style="font:400 clamp(32px,3.2vw,50px)/1.07 var(--nv-heading)">{{ nv_tr($stat, 'label') }}</div>
                                <div class="nv-eyebrow nv-text-muted" style="line-height:1.5;margin-top:12px">{{ nv_tr($stat, 'value') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div data-reveal class="nv-reveal" style="flex:1 1 320px;margin-top:clamp(0px,5vw,90px)">
            <div class="nv-frame" style="aspect-ratio:3/4">
                <img data-parallax="0.05" class="nv-frame__parallax"
                     src="{{ nv_img($section->text('media_id'), 1200, 1600) }}" alt="" {!! nv_media_edit('section', $section->id, 'media_id') !!}>
            </div>
            <div style="display:flex;gap:14px;align-items:baseline;margin-top:18px">
                <span style="width:22px;height:1px;background:var(--nv-gold-deep);margin-top:9px"></span>
                <div style="font:400 14px/1.7 var(--nv-font);color:var(--nv-muted);max-width:34ch"
                     {!! nv_edit('section', $section->id, 'caption') !!}>@t($section->text('caption'))</div>
            </div>
        </div>
    </div>
</section>

@php $cards = $section->data['cards'] ?? []; @endphp

<section class="nv-section nv-bg-light">
    <div class="nv-container">
        <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start">
            <span class="nv-numeral nv-text-gold-deep" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
            <h2 class="nv-h2" style="max-width:16ch" {!! nv_edit('section', $section->id, 'heading') !!}>
                {!! nv_accent($section->text('heading')) !!}
            </h2>
        </div>

        <div data-reveal class="nv-reveal" style="position:relative;margin-top:clamp(34px,4vw,60px)">
            <div class="nv-frame" style="aspect-ratio:21/9">
                <img data-parallax="0.045" class="nv-frame__parallax"
                     src="{{ nv_img($section->text('wide_media_id'), 2200, 950) }}" alt="" {!! nv_media_edit('section', $section->id, 'wide_media_id') !!}>
            </div>
            <div style="background:var(--nv-ink);border-radius:18px;padding:clamp(24px,3vw,40px);max-width:min(520px,90%);position:relative;margin-inline-start:clamp(0px,3vw,60px);margin-top:-60px">
                <div class="nv-eyebrow nv-eyebrow--sm nv-text-gold" style="letter-spacing:.1em" {!! nv_edit('section', $section->id, 'card_eyebrow') !!}>@t($section->text('card_eyebrow'))</div>
                <p style="font:400 clamp(16.5px,1.2vw,20px)/1.7 var(--nv-font);color:rgba(255,255,255,.8);margin:18px 0 0"
                   {!! nv_edit('section', $section->id, 'card_body') !!}>@t($section->text('card_body'))</p>
            </div>
        </div>

        <div style="display:flex;gap:clamp(20px,2.4vw,42px);flex-wrap:wrap;margin-top:clamp(30px,4vw,56px)">
            @foreach ($cards as $index => $card)
                <div data-reveal class="nv-reveal" style="flex:1 1 300px;{{ $index === 1 ? 'margin-top:clamp(0px,4vw,70px)' : '' }}">
                    <div class="nv-frame" style="aspect-ratio:4/3">
                        <img class="nv-frame__zoom" src="{{ nv_img($card['media_id'] ?? null, 1200, 900) }}" alt="">
                    </div>
                    <h3 class="nv-h3" style="font-size:clamp(23px,2vw,31px);margin:22px 0 0">
                        @t(data_get($card, 'title.'.$locale) ?: data_get($card, 'title.en'))
                    </h3>
                    <p style="font:400 16px/1.8 var(--nv-font);color:var(--nv-body);margin:12px 0 0;max-width:42ch">
                        @t(data_get($card, 'body.'.$locale) ?: data_get($card, 'body.en'))
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

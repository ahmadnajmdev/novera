<section class="nv-pagehero" style="height:{{ $section->setting('height', 'min(78vh,760px)') }};min-height:460px">
    <img data-parallax="0.05" class="nv-frame__parallax"
         style="position:absolute;width:100%;object-fit:cover;animation:nvKen 28s ease-out both"
         src="{{ nv_img($section->text('media_id'), 2200, 1300) }}" alt="" {!! nv_media_edit('section', $section->id, 'media_id') !!}>
    <div class="nv-pagehero__scrim" style="background:linear-gradient(180deg,rgba(11,15,34,.7),rgba(11,15,34,.55))"></div>
    <div class="nv-pagehero__inner">
        <div class="nv-container nv-pagehero__copy">
            <div class="nv-eyebrow nv-text-gold" style="letter-spacing:.11em" {!! nv_edit('section', $section->id, 'eyebrow') !!}>
                @t($section->text('eyebrow'))
            </div>
            <h1 class="nv-h1 nv-on-dark" style="font-size:clamp(40px,7vw,112px);line-height:1.1;margin:22px 0 0;max-width:18ch;animation:nvUp 1.4s var(--nv-ease) .2s both"
                {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h1>
        </div>
    </div>
</section>

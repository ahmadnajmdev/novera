<section style="position:relative;overflow:hidden;background:var(--nv-ink)">
    <img src="{{ nv_img($section->text('media_id'), 2200, 1200) }}" alt="" {!! nv_media_edit('section', $section->id, 'media_id') !!}
         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;mix-blend-mode:luminosity;opacity:.45">

    <div data-reveal class="nv-reveal"
         style="position:relative;max-width:1000px;margin-inline:auto;padding:clamp(90px,15vh,190px) var(--nv-gutter);text-align:center">
        <span style="display:block;width:1px;height:52px;background:linear-gradient(180deg,transparent,var(--nv-gold));margin:0 auto 34px"></span>
        <h2 class="nv-h2 nv-on-dark nv-heading-gold" style="font-size:clamp(34px,5.8vw,96px);line-height:1.07"
            {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h2>
        <a href="{{ nv_url($section->text('button_page') ?: 'contact') }}" class="nv-btn nv-btn--block" style="margin-top:clamp(34px,4vw,54px)">
            <span {!! nv_edit('section', $section->id, 'button_label') !!}>@t($section->text('button_label'))</span>
        </a>
    </div>
</section>

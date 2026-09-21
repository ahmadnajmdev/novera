@php $standards = nv_site()->collection($section->setting('collection', 'standards')); @endphp

<section class="nv-bg-white" style="padding-block:clamp(70px,10vh,140px)">
    <div class="nv-container" style="display:flex;gap:clamp(34px,5vw,90px);flex-wrap:wrap">
        <div data-reveal class="nv-reveal" style="flex:1 1 380px">
            <div class="nv-eyebrow nv-text-muted" style="letter-spacing:.1em" {!! nv_edit('section', $section->id, 'eyebrow') !!}>
                @t($section->text('eyebrow'))
            </div>
            <h2 class="nv-h2" style="font-size:clamp(27px,3.2vw,49px);line-height:1.17;margin:20px 0 0;max-width:20ch"
                {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h2>
            <p style="font:400 17px/1.85 var(--nv-font);color:var(--nv-body);margin:24px 0 0" {!! nv_edit('section', $section->id, 'body_one') !!}>
                @t($section->text('body_one'))
            </p>
            <p style="font:400 17px/1.85 var(--nv-font);color:var(--nv-body);margin:18px 0 0" {!! nv_edit('section', $section->id, 'body_two') !!}>
                @t($section->text('body_two'))
            </p>
        </div>

        <div data-reveal class="nv-reveal" style="flex:1 1 340px">
            <div class="nv-eyebrow nv-text-muted" style="letter-spacing:.1em" {!! nv_edit('section', $section->id, 'standards_eyebrow') !!}>@t($section->text('standards_eyebrow'))</div>
            <div style="margin-top:20px;border-top:1px solid rgba(19,25,54,.14)">
                @foreach ($standards as $standard)
                    <div style="display:flex;gap:20px;align-items:baseline;justify-content:space-between;padding:18px 0;border-bottom:1px solid rgba(19,25,54,.14)">
                        <span style="font:500 clamp(21px,1.7vw,28px)/1.2 var(--nv-heading)">{{ nv_tr($standard, 'label') }}</span>
                        <span style="font:400 14.5px/1.65 var(--nv-font);color:var(--nv-muted);text-align:end;max-width:26ch">{{ nv_tr($standard, 'value') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

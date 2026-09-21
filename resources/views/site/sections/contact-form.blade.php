@php
    $rows = nv_site()->collection($section->setting('collection', 'contact_rows'));
    $socials = nv_site()->collection('socials');
@endphp

<section class="nv-bg-white" style="padding-block:clamp(50px,7vw,100px) clamp(70px,10vh,140px)">
    <div class="nv-container" style="display:flex;gap:clamp(38px,5vw,96px);flex-wrap:wrap;align-items:flex-start">
        <div data-reveal class="nv-reveal" style="flex:1 1 400px">
            <div class="nv-eyebrow nv-text-muted" style="letter-spacing:.15em" {!! nv_edit('section', $section->id, 'eyebrow') !!}>
                @t($section->text('eyebrow'))
            </div>
            <livewire:enquiry-form :form-key="$section->setting('form', 'enquiry')" />
        </div>

        <div data-reveal class="nv-reveal" style="flex:1 1 320px">
            @foreach ($rows as $row)
                <div style="padding:22px 0;border-bottom:1px solid rgba(19,25,54,.14)">
                    <div class="nv-eyebrow nv-eyebrow--sm nv-text-muted" style="letter-spacing:.1em">{{ nv_tr($row, 'label') }}</div>
                    <div style="font:400 17px/1.65 var(--nv-font);color:var(--nv-navy);margin-top:11px">
                        @if ($row->url)<a href="{{ $row->url }}">{{ nv_tr($row, 'value') }}</a>@else{{ nv_tr($row, 'value') }}@endif
                    </div>
                </div>
            @endforeach

            <div class="nv-frame" style="aspect-ratio:4/3;margin-top:30px">
                <img src="{{ nv_img($section->text('media_id'), 1200, 900) }}" alt="" {!! nv_media_edit('section', $section->id, 'media_id') !!}
                     style="mix-blend-mode:luminosity;opacity:.9">
                <div style="position:absolute;inset:0;display:flex;align-items:flex-end;padding:22px">
                    <a href="{{ nv_setting('contact.map_url') ?: '#' }}" class="nv-eyebrow nv-text-gold">
                        @t(nv_setting('contact.map_label'))
                    </a>
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:11px;margin-top:22px">
                @foreach ($socials as $social)
                    <a href="{{ $social->url ?: '#' }}" class="nv-pill" style="padding:12px 20px">{{ nv_tr($social, 'label') }}</a>
                @endforeach
            </div>
        </div>
    </div>
</section>

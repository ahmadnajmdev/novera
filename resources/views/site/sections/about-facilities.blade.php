@php $facilities = nv_site()->collection($section->setting('collection', 'facilities')); @endphp

<section class="nv-bg-white" style="padding-block:0 clamp(70px,10vh,140px)">
    <div class="nv-container">
        <div data-reveal class="nv-reveal nv-frame" style="aspect-ratio:21/9">
            <img data-parallax="0.05" style="position:absolute;inset:-10% 0;width:100%;height:120%;object-fit:cover"
                 src="{{ nv_img($section->text('media_id'), 2200, 950) }}" alt="" {!! nv_media_edit('section', $section->id, 'media_id') !!}>
        </div>

        <div class="nv-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:clamp(24px,3vw,56px);margin-top:clamp(34px,4vw,60px)">
            @foreach ($facilities as $facility)
                <div data-reveal class="nv-reveal" style="padding-top:22px;border-top:1px solid rgba(19,25,54,.16)">
                    <div class="nv-eyebrow nv-eyebrow--sm nv-text-gold-deep">
                        {{ nv_t(data_get($facility->extra, 'tag.'.$locale) ?: data_get($facility->extra, 'tag.en')) }}
                    </div>
                    <h3 class="nv-h3" style="margin:16px 0 0">{{ nv_tr($facility, 'label') }}</h3>
                    <p style="font:400 15.5px/1.8 var(--nv-font);color:var(--nv-body);margin:12px 0 0">{{ nv_tr($facility, 'value') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

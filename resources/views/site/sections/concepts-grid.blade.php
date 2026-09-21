@php $concepts = \App\Models\Concept::active()->with('media')->get(); @endphp

<section class="nv-bg-ink" style="padding-bottom:clamp(60px,8vw,110px)">
    <div class="nv-container nv-grid" style="grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:clamp(16px,1.8vw,32px)">
        @foreach ($concepts as $index => $concept)
            @php $ratio = $index % 5 === 0 ? '4/5' : ($index % 3 === 0 ? '1/1' : '3/4'); @endphp
            <a href="{{ nv_entity_url($concept) }}" data-reveal class="nv-card nv-reveal">
                <div class="nv-frame" style="aspect-ratio:{{ $ratio }}">
                    <img loading="lazy" decoding="async" class="nv-frame__duotone"
                         src="{{ nv_img($concept->media, 900, 1250) }}" alt="{{ nv_tr($concept, 'name') }}">
                    <span class="nv-frame__hairline"></span>
                </div>
                <div style="display:flex;align-items:baseline;gap:14px;margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,.16)">
                    <span class="nv-eyebrow nv-eyebrow--sm nv-text-gold" style="letter-spacing:.12em">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="nv-card__name nv-on-dark">{{ nv_tr($concept, 'name') }}</span>
                </div>
                <p style="font:400 16px/1.75 var(--nv-font);color:rgba(255,255,255,.45);margin:10px 0 0;max-width:36ch">
                    {{ nv_tr($concept, 'blurb') }}
                </p>
            </a>
        @endforeach
    </div>
</section>

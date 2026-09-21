<div style="display:flex;gap:clamp(28px,4vw,80px);flex-wrap:wrap;margin-top:clamp(30px,4vw,54px)">
    <div style="flex:1 1 360px">
        @foreach ($materials as $material)
            <a href="{{ nv_entity_url($material) }}"
               wire:mouseenter="highlight({{ $material->id }})"
               class="nv-matlist__row {{ $active?->is($material) ? 'is-active' : '' }}">
                <span class="nv-matlist__name">{{ nv_tr($material, 'name') }}</span>
                <span class="nv-matlist__tag">{{ nv_tr($material, 'tag') }}</span>
            </a>
        @endforeach
    </div>

    <div class="nv-matpreview">
        <div class="nv-frame">
            @if ($active)
                <img wire:key="preview-{{ $active->id }}" loading="lazy" decoding="async"
                     src="{{ nv_img($active->media, 1000, 1330) }}" alt="{{ nv_tr($active, 'name') }}">
            @endif
            <span class="nv-matpreview__border"></span>
        </div>
        <div style="display:flex;justify-content:space-between;gap:16px;align-items:baseline;margin-top:18px">
            <div style="font:500 clamp(21px,1.7vw,28px)/1.2 var(--nv-heading);color:#fff">{{ nv_tr($active, 'name') }}</div>
            <a href="{{ nv_url($ctaPage) }}" class="nv-eyebrow nv-eyebrow--sm nv-text-gold"><span
                @if ($sectionId) {!! nv_edit('section', $sectionId, 'cta_label') !!} @endif
            >@t($ctaLabel)</span></a>
        </div>
        <p style="font:400 15px/1.75 var(--nv-font);color:rgba(255,255,255,.5);margin:12px 0 0;max-width:38ch">
            {{ nv_tr($active, 'blurb') }}
        </p>
    </div>
</div>

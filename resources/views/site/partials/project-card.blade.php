@php
    $index = $index ?? 0;
    $dark = $dark ?? false;
    $ratio = $index % 4 === 0 ? '4/5' : ($index % 3 === 0 ? '1/1' : '3/4');
    $offset = $index % 3 === 1 ? 'clamp(0px,4vw,80px)' : '0px';
    $showHover = $showHover ?? false;
@endphp

<a href="{{ nv_entity_url($project) }}" data-reveal class="nv-card nv-reveal" style="margin-top:{{ $offset }}">
    <div class="nv-frame" style="aspect-ratio:{{ $ratio }}">
        <img loading="lazy" decoding="async" class="nv-frame__zoom"
             src="{{ nv_img($project->media, 1100, 1450) }}" alt="{{ nv_tr($project, 'name') }}">
        @if ($showHover)
            <div class="nv-card__hoverveil"></div>
            <div class="nv-card__hoverlabel">
                <span class="rule"></span>
                <span class="nv-eyebrow nv-on-dark">@t('View Project')</span>
            </div>
        @else
            <span class="nv-frame__hairline" style="inset:14px;border-radius:2px"></span>
        @endif
    </div>
    <div class="nv-card__meta {{ $dark ? 'nv-card__meta--dark' : '' }}" style="margin-top:{{ $dark ? '22px' : '20px' }};padding-top:{{ $dark ? '18px' : '16px' }}">
        <div>
            <div class="nv-card__name {{ $dark ? 'nv-on-dark' : '' }}" style="{{ $dark ? 'font-size:clamp(22px,1.8vw,31px)' : '' }}">{{ nv_tr($project, 'name') }}</div>
            <div class="nv-card__sub" style="color:{{ $dark ? 'rgba(255,255,255,.45)' : 'var(--nv-muted)' }}">
                {{ nv_tr($project, 'location') }} — {{ nv_tr($project->category, 'name') }}
            </div>
        </div>
        <div class="nv-card__status" style="color:{{ $dark ? 'var(--nv-gold)' : 'var(--nv-gold-deep)' }}">
            {{ nv_tr($project->status, 'name') }} {{ $project->year }}
        </div>
    </div>
</a>

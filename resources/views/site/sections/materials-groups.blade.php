@php $groups = \App\Models\MaterialGroup::with('materials.media')->orderBy('sort')->get(); @endphp

@foreach ($groups as $group)
    <section style="background:{{ $group->background }};padding-block:clamp(50px,7vw,100px)">
        <div class="nv-container">
            <div style="display:flex;align-items:baseline;gap:16px;padding-bottom:28px;border-bottom:1px solid {{ $group->line }}">
                <span style="width:28px;height:1px;background:var(--nv-gold-deep);margin-bottom:6px"></span>
                <h2 style="font:700 13px/1 var(--nv-heading);letter-spacing:.11em;text-transform:uppercase;color:{{ $group->foreground }};margin:0"
                    {!! nv_edit('material_group', $group->id, 'name') !!}>{{ nv_tr($group, 'name') }}</h2>
            </div>

            <div class="nv-grid nv-grid--tight" style="gap:clamp(18px,2.2vw,38px);margin-top:clamp(30px,3.5vw,50px)">
                @foreach ($group->materials as $material)
                    <a href="{{ nv_entity_url($material) }}" data-reveal class="nv-card nv-reveal">
                        <div class="nv-frame" style="aspect-ratio:3/4">
                            <img loading="lazy" decoding="async" class="nv-frame__zoom"
                                 src="{{ nv_img($material->media, 900, 1200) }}" alt="{{ nv_tr($material, 'name') }}">
                            <span class="nv-frame__hairline" style="inset:0;border-radius:var(--nv-radius)"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;gap:12px;margin-top:18px">
                            <div class="nv-card__name" style="color:{{ $group->foreground }}">{{ nv_tr($material, 'name') }}</div>
                            <span style="font:400 13px/1 var(--nv-font);color:var(--nv-gold-deep)">→</span>
                        </div>
                        <p style="font:400 16px/1.75 var(--nv-font);color:{{ $group->muted }};margin:9px 0 0;max-width:32ch">
                            {{ nv_tr($material, 'blurb') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endforeach

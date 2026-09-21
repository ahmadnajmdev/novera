@php $concepts = \App\Models\Concept::active()->with('media')->get(); @endphp

<section class="nv-section nv-bg-white" style="padding-block:clamp(80px,12vh,160px) clamp(60px,8vh,110px)">
    <div class="nv-container" style="display:flex;justify-content:space-between;align-items:flex-end;gap:30px;flex-wrap:wrap">
        <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start">
            <span class="nv-numeral nv-text-gold-deep" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
            <div>
                <h2 class="nv-h2" style="max-width:20ch" {!! nv_edit('section', $section->id, 'heading') !!}>
                    {!! nv_accent($section->text('heading')) !!}
                </h2>
                <p class="nv-body nv-text-body" style="margin:22px 0 0;max-width:48ch" {!! nv_edit('section', $section->id, 'body') !!}>
                    @t($section->text('body'))
                </p>
            </div>
        </div>
        <div class="nv-eyebrow nv-text-muted" {!! nv_edit('section', $section->id, 'hint') !!}>@t($section->text('hint'))</div>
    </div>

    <div class="nv-rail" data-rail style="margin-top:clamp(34px,4vw,60px)">
        <div class="nv-rail__track">
            @foreach ($concepts->concat($concepts) as $position => $concept)
                @php $index = $position % max($concepts->count(), 1); @endphp
                <a href="{{ nv_entity_url($concept) }}" class="nv-rail__item">
                    <div class="nv-frame" style="aspect-ratio:3/4.4">
                        <img loading="lazy" decoding="async" class="nv-frame__duotone"
                             src="{{ nv_img($concept->media, 800, 1180) }}" alt="{{ nv_tr($concept, 'name') }}">
                        <div class="nv-rail__caption">
                            <span style="font:500 clamp(20px,1.5vw,26px)/1.2 var(--nv-heading);color:#fff">{{ nv_tr($concept, 'name') }}</span>
                            <span class="nv-eyebrow nv-text-gold">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="nv-container" style="margin-top:clamp(30px,4vw,52px)">
        <a href="{{ nv_url($section->text('cta_page') ?: 'concepts') }}" class="nv-btn nv-btn--outline">
            <span {!! nv_edit('section', $section->id, 'cta_label') !!}>@t($section->text('cta_label'))</span>
        </a>
    </div>
</section>

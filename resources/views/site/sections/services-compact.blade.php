@php $services = \App\Models\Service::active()->with('media')->get(); @endphp

<section class="nv-section nv-bg-white">
    <div class="nv-container">
        <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start;padding-bottom:clamp(24px,3vw,44px)">
            <span class="nv-numeral nv-text-gold-deep" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
            <h2 class="nv-h2" style="max-width:20ch" {!! nv_edit('section', $section->id, 'heading') !!}>
                {!! nv_accent($section->text('heading')) !!}
            </h2>
        </div>

        @foreach ($services as $service)
            <div data-reveal class="nv-reveal"
                 style="display:flex;gap:clamp(24px,3.5vw,70px);flex-wrap:wrap;align-items:center;padding:clamp(30px,3.5vw,54px) 0;border-top:1px solid rgba(19,25,54,.14)">
                <div class="nv-numeral nv-numeral--lg nv-text-gold-deep">{{ $service->number }} —</div>
                <div style="flex:1 1 320px">
                    <h3 class="nv-h3" style="font-size:clamp(25px,2.4vw,39px);line-height:1.22" {!! nv_edit('service', $service->id, 'name') !!}>
                        {{ nv_tr($service, 'name') }}
                    </h3>
                    <p class="nv-body nv-text-body" style="margin:16px 0 0;max-width:48ch" {!! nv_edit('service', $service->id, 'body') !!}>
                        {{ nv_tr($service, 'body') }}
                    </p>
                </div>
                <div class="nv-frame" style="flex:1 1 260px;aspect-ratio:16/10">
                    <img loading="lazy" decoding="async" class="nv-frame__duotone" style="filter:none"
                         src="{{ nv_img($service->media, 1200, 750) }}" alt="{{ nv_tr($service, 'name') }}">
                </div>
            </div>
        @endforeach
    </div>
</section>

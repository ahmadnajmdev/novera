@php $services = \App\Models\Service::active()->with('media', 'points')->get(); @endphp

<section class="nv-bg-white" style="padding-bottom:clamp(70px,10vh,140px)">
    <div class="nv-container">
        @foreach ($services as $service)
            <div data-reveal class="nv-reveal"
                 style="display:flex;gap:clamp(24px,3.5vw,70px);flex-wrap:wrap;padding:clamp(34px,4.5vw,66px) 0;border-top:1px solid rgba(19,25,54,.14)">
                <div style="flex:1 1 340px">
                    <div class="nv-numeral nv-numeral--lg nv-text-gold-deep">{{ $service->number }}</div>
                    <h2 class="nv-h2" style="font-size:clamp(27px,3vw,47px);line-height:1.17;margin:10px 0 0"
                        {!! nv_edit('service', $service->id, 'name') !!}>{{ nv_tr($service, 'name') }}</h2>
                    <p style="font:400 17px/1.85 var(--nv-font);color:var(--nv-body);margin:20px 0 0;max-width:46ch"
                       {!! nv_edit('service', $service->id, 'body') !!}>{{ nv_tr($service, 'body') }}</p>

                    <div style="display:grid;gap:11px;margin-top:26px">
                        @foreach ($service->points as $point)
                            <div style="display:flex;gap:14px;align-items:baseline">
                                <span style="width:14px;height:1px;background:var(--nv-gold-deep);flex-shrink:0;margin-top:10px"></span>
                                <span style="font:400 16px/1.75 var(--nv-font);color:var(--nv-navy)">{{ nv_tr($point, 'text') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="nv-frame" style="flex:1 1 300px;aspect-ratio:4/3">
                    <img loading="lazy" decoding="async" data-parallax="0.035" class="nv-frame__parallax"
                         src="{{ nv_img($service->media, 1200, 900) }}" alt="{{ nv_tr($service, 'name') }}">
                </div>
            </div>
        @endforeach
    </div>
</section>

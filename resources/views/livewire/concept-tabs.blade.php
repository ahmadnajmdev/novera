<div>
    <div class="nv-subbar">
        <div class="nv-container nv-subbar__inner">
            @foreach ($tabs as $item)
                <button type="button"
                        wire:click="select(@js($item->key))"
                        class="nv-tab {{ $active?->is($item) ? 'is-active' : '' }}">{{ nv_tr($item, 'label') }}</button>
            @endforeach
        </div>
    </div>

    <section class="nv-bg-white" style="padding-block:clamp(50px,7vw,100px) clamp(70px,10vh,140px)">
        <div class="nv-container">
            <h2 class="nv-h2" style="font-size:clamp(27px,3.4vw,53px);line-height:1.15;margin:0 0 clamp(34px,4vw,64px);max-width:24ch">
                {{ $heading }}
            </h2>

            <div class="nv-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:clamp(18px,2.2vw,40px)">
                @foreach ($items as $index => $item)
                    <div wire:key="item-{{ $active?->key }}-{{ $index }}" data-reveal class="nv-reveal">
                        <div class="nv-frame" style="aspect-ratio:4/5">
                            <img loading="lazy" decoding="async" class="nv-frame__zoom"
                                 src="{{ nv_img($item['media'], 900, 1150) }}" alt="{{ $item['title'] }}">
                            <span class="nv-frame__hairline"></span>
                        </div>
                        <div style="display:flex;align-items:baseline;gap:14px;margin-top:20px;padding-top:16px;border-top:1px solid rgba(19,25,54,.14)">
                            <span class="nv-eyebrow nv-eyebrow--sm nv-text-gold-deep" style="letter-spacing:.12em">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 class="nv-h3" style="font-size:clamp(20px,1.6vw,27px);line-height:1.25">{{ $item['title'] }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

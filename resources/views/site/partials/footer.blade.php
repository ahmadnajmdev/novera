@php
    $site = nv_site();
    $footerMenu = $site->menu('footer');
    $contactRows = $site->collection('contact_rows');
    $socials = $site->collection('socials');
@endphp

<footer class="nv-footer">
    <div class="nv-container">
        <div class="nv-footer__cols">
            <div class="nv-footer__brand" style="flex:1 1 300px">
                <img data-fb-h src="{{ nv_media(nv_setting('brand.logo_gold')) }}" alt="{{ nv_setting('brand.name') }}">
                <img data-fb-v src="{{ nv_media(nv_setting('brand.logo_vertical')) }}" alt="{{ nv_setting('brand.name') }}">
                <p style="font:400 16px/1.8 var(--nv-font);color:rgba(255,255,255,.5);margin:26px 0 0;max-width:32ch">
                    @t(nv_setting('brand.tagline'))
                </p>
            </div>

            <div style="flex:1 1 180px">
                <div class="nv-footer__heading">@t('Navigate')</div>
                <div class="nv-footer__list">
                    @foreach ($footerMenu as $item)
                        <a href="{{ $item->href($locale) }}">{{ nv_tr($item, 'label') }}</a>
                    @endforeach
                </div>
            </div>

            <div style="flex:1 1 240px">
                <div class="nv-footer__heading">@t('Contact')</div>
                <div class="nv-footer__list">
                    @foreach ($contactRows as $row)
                        @if ($row->url)
                            <a href="{{ $row->url }}">{{ nv_tr($row, 'value') }}</a>
                        @else
                            <div>{{ nv_tr($row, 'value') }}</div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div style="flex:1 1 160px">
                <div class="nv-footer__heading">@t('Follow')</div>
                <div class="nv-footer__list">
                    @foreach ($socials as $social)
                        <a href="{{ $social->url ?: '#' }}" @if($social->url && $social->url !== '#') target="_blank" rel="noopener" @endif>
                            {{ nv_tr($social, 'label') }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="nv-footer__base">
            <div>@t(nv_setting('brand.copyright'))</div>
            <div>@t(nv_setting('brand.credit'))</div>
        </div>
    </div>
</footer>

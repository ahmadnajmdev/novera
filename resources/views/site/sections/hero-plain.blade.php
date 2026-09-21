@php
    $bg = $section->setting('background', 'ink');
    $dark = in_array($bg, ['ink', 'navy'], true);
@endphp

<section class="nv-section--head nv-bg-{{ $bg }}" style="padding-block:clamp(155px,19vh,225px) {{ $section->text('intro') ? 'clamp(36px,5vw,64px)' : 'clamp(30px,4vw,54px)' }}">
    <div class="nv-container">
        <div class="nv-eyebrow {{ $dark ? 'nv-text-gold' : 'nv-text-muted' }}" style="letter-spacing:.11em"
             {!! nv_edit('section', $section->id, 'eyebrow') !!}>@t($section->text('eyebrow'))</div>

        <h1 class="nv-h1 {{ $dark ? 'nv-on-dark' : '' }}"
            style="margin:22px 0 0;max-width:20ch;animation:nvUp 1.3s var(--nv-ease) .1s both"
            {!! nv_edit('section', $section->id, 'heading') !!}>{!! nv_accent($section->text('heading')) !!}</h1>

        @if ($section->text('intro'))
            <p style="font:400 17px/1.8 var(--nv-font);margin:26px 0 0;max-width:54ch;color:{{ $dark ? 'rgba(255,255,255,.58)' : 'var(--nv-body)' }}"
               {!! nv_edit('section', $section->id, 'intro') !!}>@t($section->text('intro'))</p>
        @endif
    </div>
</section>

@php
    // Block types map 1:1 to a view; an unknown type renders nothing rather
    // than breaking the page, which matters when a block is renamed.
    $view = 'site.sections.'.str_replace('.', '-', $section->type);
@endphp

@if (view()->exists($view))
    <div data-nv-section="{{ $section->id }}" data-nv-section-label="{{ $section->type }}">
        @include($view, ['section' => $section])
    </div>
@endif

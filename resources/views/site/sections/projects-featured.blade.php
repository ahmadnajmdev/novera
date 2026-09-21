@php
    $projects = \App\Models\Project::active()
        ->where('is_featured', true)
        ->with('media', 'category', 'status')
        ->take($section->setting('limit', 3))
        ->get();
@endphp

<section class="nv-section nv-bg-ink">
    <div class="nv-container">
        <div data-reveal class="nv-reveal"
             style="display:flex;justify-content:space-between;align-items:flex-end;gap:34px;flex-wrap:wrap;padding-bottom:clamp(34px,4vw,60px);border-bottom:1px solid rgba(255,255,255,.14)">
            <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start">
                <span class="nv-numeral nv-text-gold" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
                <h2 class="nv-h2 nv-on-dark" style="max-width:18ch" {!! nv_edit('section', $section->id, 'heading') !!}>
                    {!! nv_accent($section->text('heading')) !!}
                </h2>
            </div>
            <a href="{{ nv_url($section->text('link_page') ?: 'projects') }}" class="nv-link-underline">
                <span {!! nv_edit('section', $section->id, 'link_label') !!}>@t($section->text('link_label'))</span>
            </a>
        </div>

        <div class="nv-grid nv-grid--cards" style="gap:clamp(20px,2.4vw,42px);margin-top:clamp(34px,4vw,64px)">
            @foreach ($projects as $index => $project)
                @include('site.partials.project-card', ['project' => $project, 'index' => $index, 'dark' => true])
            @endforeach
        </div>
    </div>
</section>

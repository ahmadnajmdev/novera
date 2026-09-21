<div>
    @if ($showFilters)
        <div class="nv-subbar nv-subbar--bordered">
            <div class="nv-container nv-subbar__inner" style="gap:clamp(14px,2.2vw,36px)">
                @foreach ($filters as $item)
                    @php $key = $keyFor($item); @endphp
                    <button type="button"
                            wire:click="select(@js($key))"
                            class="nv-tab {{ $key === $activeKey ? 'is-active' : '' }}"
                            style="padding:18px 0">{{ nv_tr($item, 'label') }}</button>
                @endforeach
            </div>
        </div>
    @endif

    <section class="nv-bg-white" style="padding-block:clamp(38px,5vw,70px) clamp(70px,10vh,140px)">
        <div class="nv-container nv-grid nv-grid--cards" style="gap:clamp(20px,2.4vw,44px)">
            @forelse ($projects as $index => $project)
                <div wire:key="project-{{ $project->id }}">
                    @include('site.partials.project-card', ['project' => $project, 'index' => $index, 'showHover' => true])
                </div>
            @empty
                <p class="nv-body nv-text-muted">@t('No projects match this filter yet.')</p>
            @endforelse
        </div>
    </section>
</div>

<section class="nv-section nv-bg-navy">
    <div class="nv-container">
        <div style="display:flex;gap:clamp(18px,2.5vw,44px);align-items:flex-start;padding-bottom:clamp(30px,3.5vw,52px);border-bottom:1px solid rgba(255,255,255,.16)">
            <span class="nv-numeral nv-text-gold" {!! nv_edit('section', $section->id, 'number') !!}>{{ $section->text('number') }}</span>
            <div>
                <h2 class="nv-h2 nv-on-dark" style="max-width:18ch" {!! nv_edit('section', $section->id, 'heading') !!}>
                    {!! nv_accent($section->text('heading')) !!}
                </h2>
                <p class="nv-body nv-on-dark-muted" style="margin:20px 0 0;max-width:50ch" {!! nv_edit('section', $section->id, 'body') !!}>
                    @t($section->text('body'))
                </p>
            </div>
        </div>

        <livewire:material-index :cta-label="$section->text('cta_label')" :cta-page="$section->text('cta_page') ?: 'materials'" :section-id="$section->id" />
    </div>
</section>

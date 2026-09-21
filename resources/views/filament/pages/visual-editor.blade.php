@php
    $widths = ['desktop' => '100%', 'tablet' => '834px', 'mobile' => '390px'];
    $sections = $this->getSections();
@endphp

<x-filament-panels::page>
    <div
        x-data="noveraEditor(@js($this->getPreviewUrl()))"
        class="nv-editor"
        wire:ignore.self
    >
        {{-- Toolbar --}}
        <div class="nv-editor__bar">
            <label class="nv-editor__control">
                <span>Which page</span>
                <select wire:model.live="pageId">
                    @foreach ($this->getPages() as $item)
                        <option value="{{ $item->id }}">{{ nv_tr($item, 'title', config('app.fallback_locale')) }}</option>
                    @endforeach
                </select>
            </label>

            <label class="nv-editor__control">
                <span>Language</span>
                <select wire:model.live="locale">
                    @foreach ($this->getLocales() as $item)
                        <option value="{{ $item->code }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </label>

            <div class="nv-editor__devices">
                @foreach ($widths as $key => $width)
                    <button type="button"
                            @click="device = @js($key)"
                            :class="device === @js($key) ? 'is-active' : ''">{{ ucfirst($key) }}</button>
                @endforeach
            </div>

            <div class="nv-editor__spacer"></div>

            <a href="{{ $this->getPreviewUrl() }}" target="_blank" rel="noopener" class="nv-editor__ghost">
                Open in new tab ↗
            </a>
            <button type="button" class="nv-editor__ghost" @click="reload()">Reload</button>
            <button type="button" class="nv-editor__ghost" @click="panel = !panel"
                    x-text="panel ? 'Hide panel' : 'Show panel'"></button>
        </div>

        <div class="nv-editor__body">
            {{-- Preview --}}
            <div class="nv-editor__stage">
                <div class="nv-editor__frame" :style="`width: ${widths[device]}`">
                    <iframe x-ref="frame" src="{{ $this->getPreviewUrl() }}" title="Site preview"></iframe>
                </div>
            </div>

            {{-- Panel: what you are editing, or the page outline --}}
            <aside class="nv-editor__panel" x-show="panel" x-cloak>
                @if ($mediaSelection)
                    <div class="nv-editor__panel-head">
                        <div>
                            <div class="nv-editor__kicker">Picture</div>
                            <h3>Choose a picture</h3>
                        </div>
                        <button type="button" wire:click="clearMediaSelection" class="nv-editor__close" aria-label="Close">✕</button>
                    </div>

                    <p class="nv-editor__hint">Click one to put it on the page. It changes straight away.</p>

                    <div class="nv-editor__media">
                        @foreach ($this->getMediaLibrary() as $item)
                            <button type="button"
                                    wire:click="chooseMedia({{ $item->id }})"
                                    wire:key="media-{{ $item->id }}"
                                    @class(['nv-editor__thumb', 'is-on' => (int) $mediaSelection['current'] === $item->id])
                                    title="{{ $item->filename }}">
                                <img src="{{ nv_img($item, 220, 160) }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>

                    <p class="nv-editor__hint">
                        To upload something new, go to <strong>Media &amp; lists → Photos &amp; files</strong>,
                        then come back here.
                    </p>
                @elseif ($selection)
                    <div class="nv-editor__panel-head">
                        <div>
                            <div class="nv-editor__kicker">You are editing</div>
                            <h3>{{ $this->getFieldLabel() }}</h3>
                        </div>
                        <button type="button" wire:click="clearSelection" class="nv-editor__close" aria-label="Close">✕</button>
                    </div>

                    <div class="nv-editor__locale">
                        In <strong>{{ $this->getLocales()->firstWhere('code', $selection['locale'])?->name ?? $selection['locale'] }}</strong>
                    </div>

                    <div class="nv-editor__status" wire:key="status-{{ $lastSaved }}">
                        @if ($lastSaved)
                            <span class="nv-editor__saved">✓ Saved at {{ $lastSaved }}</span>
                        @else
                            Type on the page. It saves when you click away.
                        @endif
                    </div>

                    @if ($this->isHeading())
                        <p class="nv-editor__hint">
                            Put <code>*asterisks*</code> around words you want in gold italics.
                        </p>
                    @endif

                    <p class="nv-editor__hint">
                        <strong>Enter</strong> finishes the edit. <strong>Esc</strong> does too.
                    </p>
                @else
                    <div class="nv-editor__panel-head">
                        <div>
                            <div class="nv-editor__kicker">This page</div>
                            <h3>{{ $sections->count() }} {{ Str::plural('section', $sections->count()) }}</h3>
                        </div>
                    </div>

                    <p class="nv-editor__hint">
                        Click any wording on the page and type over it. Click a picture to swap it.
                        Everything saves as you go.
                    </p>

                    <ul class="nv-editor__sections">
                        @foreach ($sections as $index => $section)
                            <li @class(['nv-editor__section', 'is-hidden' => ! $section->is_visible])
                                wire:key="section-{{ $section->id }}">
                                <button type="button"
                                        class="nv-editor__section-name"
                                        wire:click="scrollToSection({{ $section->id }})"
                                        title="Jump to this section">
                                    <span class="nv-editor__section-index">{{ $index + 1 }}</span>
                                    <span>
                                        {{ \App\Filament\Support\SectionBlocks::label($section->type) }}
                                        @unless ($section->is_visible)
                                            <em>hidden</em>
                                        @endunless
                                    </span>
                                </button>

                                <span class="nv-editor__section-tools">
                                    <button type="button" wire:click="moveSection({{ $section->id }}, 'up')"
                                            @disabled($index === 0) title="Move up">↑</button>
                                    <button type="button" wire:click="moveSection({{ $section->id }}, 'down')"
                                            @disabled($index === $sections->count() - 1) title="Move down">↓</button>
                                    <button type="button" wire:click="toggleSection({{ $section->id }})"
                                            title="{{ $section->is_visible ? 'Hide from the page' : 'Show on the page' }}">
                                        {{ $section->is_visible ? '👁' : '⃠' }}
                                    </button>
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($pageId)
                        <a class="nv-editor__add"
                           href="{{ \App\Filament\Resources\Pages\PageResource::getUrl('edit', ['record' => $pageId]) }}">
                            Add or remove sections
                        </a>
                    @endif
                @endif
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            function noveraEditor(initialUrl) {
                return {
                    device: 'desktop',
                    panel: true,
                    widths: @js($widths),
                    url: initialUrl,

                    init() {
                        // Messages arrive from the preview frame, which is same-origin;
                        // anything else is ignored outright.
                        window.addEventListener('message', (event) => {
                            if (event.origin !== window.location.origin) return;
                            const data = event.data;
                            if (!data || data.channel !== 'novera-editor') return;

                            if (data.type === 'select') {
                                this.$wire.selectNode({
                                    model: data.model,
                                    id: data.id,
                                    field: data.field,
                                    locale: data.locale,
                                    label: data.label,
                                });
                            }

                            if (data.type === 'commit') {
                                // Named around Livewire: $wire.commit is part of
                                // its own request machinery, so a PHP method of
                                // that name is never reached.
                                this.$wire.saveInlineEdit({
                                    model: data.model,
                                    id: data.id,
                                    field: data.field,
                                    locale: data.locale,
                                    value: data.value,
                                });
                            }

                            if (data.type === 'pick-media') {
                                this.$wire.pickMedia({
                                    model: data.model,
                                    id: data.id,
                                    field: data.field,
                                });
                            }
                        });

                        this.$wire.on('nv-apply', ({ payload }) => this.send('apply', payload));
                        this.$wire.on('nv-apply-media', ({ payload }) => this.send('apply-media', payload));
                        this.$wire.on('nv-deselect', () => this.send('deselect'));
                        this.$wire.on('nv-scroll-to-section', ({ id }) => this.send('scroll-to-section', { id }));
                        this.$wire.on('nv-reload-preview', ({ url }) => {
                            this.url = url;
                            this.reload();
                        });
                    },

                    send(type, payload = {}) {
                        this.$refs.frame?.contentWindow?.postMessage(
                            { channel: 'novera-editor', type, ...payload },
                            window.location.origin,
                        );
                    },

                    reload() {
                        if (this.$refs.frame) this.$refs.frame.src = this.url;
                    },
                };
            }
        </script>
    @endpush
</x-filament-panels::page>

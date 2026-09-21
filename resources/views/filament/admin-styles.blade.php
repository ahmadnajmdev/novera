{{--
    Every bit of CSS this panel adds of its own.

    It is injected into <head> by a render hook rather than pushed from the
    views that use it: a @push inside a component only reaches the layout on a
    full page load, so anything rendered into a Livewire modal — the section
    picker, for one — arrived unstyled.

    Filament 5 publishes its palette as bare oklch values in --gray-*,
    --primary-* and so on, so they are used directly and blended with
    color-mix rather than wrapped in rgb().
--}}
<style>
    .nv-start__intro { margin-bottom: 1.25rem; }
    .nv-start__greeting {
        font-size: 1.5rem; font-weight: 700; line-height: 1.2;
        color: var(--gray-950);
    }
    .nv-start__lead {
        margin-top: .375rem; max-width: 46rem; font-size: .875rem;
        line-height: 1.6; color: var(--gray-500);
    }
    .nv-start__grid {
        display: grid; gap: .75rem;
        grid-template-columns: repeat(auto-fill, minmax(19rem, 1fr));
    }
    .nv-start__card {
        display: flex; gap: .875rem; padding: 1rem;
        border: 1px solid var(--gray-200); border-radius: .75rem;
        background: color-mix(in oklab, var(--gray-50) 55%, transparent);
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .nv-start__card:hover {
        border-color: var(--primary-500);
        box-shadow: 0 1px 3px rgb(0 0 0 / .08);
        transform: translateY(-1px);
    }
    /* The first card is where a new editor should go; it gets the
       only coloured frame on the page so the eye lands there. */
    .nv-start__card--primary {
        border-color: var(--primary-400);
        background: color-mix(in oklab, var(--primary-50) 60%, transparent);
    }
    .nv-start__icon {
        flex-shrink: 0; display: grid; place-items: center;
        width: 2.5rem; height: 2.5rem; border-radius: .5rem;
        background: color-mix(in oklab, var(--primary-500) 12%, transparent);
        color: var(--primary-600);
    }
    .nv-start__body { display: flex; flex-direction: column; gap: .25rem; min-width: 0; }
    .nv-start__title {
        display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
        font-size: .875rem; font-weight: 600; color: var(--gray-950);
    }
    .nv-start__text { font-size: .8125rem; line-height: 1.5; color: var(--gray-500); }
    .nv-start__action {
        display: inline-flex; align-items: center; gap: .25rem; margin-top: .25rem;
        font-size: .8125rem; font-weight: 600; color: var(--primary-600);
    }

    /* Filament adds .dark to <html>. */
    .dark .nv-start__greeting { color: var(--gray-50); }
    .dark .nv-start__title { color: var(--gray-50); }
    .dark .nv-start__card {
        border-color: var(--gray-700);
        background: color-mix(in oklab, var(--gray-900) 55%, transparent);
    }
    .dark .nv-start__card--primary {
        border-color: var(--primary-600);
        background: color-mix(in oklab, var(--primary-950) 45%, transparent);
    }
    .dark .nv-start__icon { color: var(--primary-400); }
    .dark .nv-start__action { color: var(--primary-400); }
            

            .nv-cards__group {
    margin: 1rem 0 .5rem; font-size: .6875rem; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase; color: var(--gray-500);
            }
            .nv-cards__group:first-child { margin-top: 0; }
            .nv-cards__grid {
    display: grid; gap: .5rem;
    grid-template-columns: repeat(var(--nv-cards-columns, 2), minmax(0, 1fr));
            }
            @media (max-width: 768px) { .nv-cards__grid { grid-template-columns: 1fr; } }
            .nv-card {
    display: flex; align-items: flex-start; gap: .625rem; text-align: start;
    padding: .75rem; border-radius: .625rem; border: 1px solid var(--gray-200);
    background: var(--gray-50); transition: border-color .12s, background .12s;
            }
            .nv-card:hover { border-color: var(--primary-400); }
            .nv-card--on {
    border-color: var(--primary-500);
    background: color-mix(in oklab, var(--primary-50) 70%, transparent);
    box-shadow: 0 0 0 1px var(--primary-500);
            }
            .nv-card__icon {
    flex-shrink: 0; display: grid; place-items: center;
    width: 2rem; height: 2rem; border-radius: .5rem;
    background: color-mix(in oklab, var(--primary-500) 14%, transparent);
    color: var(--primary-600);
            }
            .nv-card__text { display: flex; flex-direction: column; gap: .15rem; min-width: 0; flex: 1; }
            .nv-card__label { font-size: .8125rem; font-weight: 600; color: var(--gray-950); }
            .nv-card__hint { font-size: .75rem; line-height: 1.45; color: var(--gray-500); }
            .nv-card__tick { flex-shrink: 0; color: var(--primary-600); opacity: 0; }
            .nv-card--on .nv-card__tick { opacity: 1; }

            .dark .nv-card { border-color: var(--gray-700); background: color-mix(in oklab, var(--gray-900) 60%, transparent); }
            .dark .nv-card__label { color: var(--gray-50); }
            .dark .nv-card--on { background: color-mix(in oklab, var(--primary-950) 50%, transparent); }
            .dark .nv-card__icon { color: var(--primary-400); }
        

    /* ── Visual editor ──────────────────────────────────────────────────
       Sized to the viewport rather than a fixed height, so the toolbar is
       never pushed off a short screen. */
    .nv-editor { display: flex; flex-direction: column; gap: .75rem; height: clamp(30rem, calc(100dvh - 13rem), 70rem); }
    .nv-editor__bar {
        display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
        padding: .625rem .875rem; border-radius: .75rem;
        background: var(--gray-50); border: 1px solid var(--gray-200);
    }
    .dark .nv-editor__bar { background: color-mix(in oklab, var(--gray-900) 70%, transparent); border-color: var(--gray-700); }
    .nv-editor__control { display: flex; align-items: center; gap: .5rem; font-size: .75rem; font-weight: 600; }
    .nv-editor__control select {
        border-radius: .5rem; border: 1px solid var(--gray-300);
        padding: .35rem .6rem; font-size: .8rem; background: white; color: var(--gray-950);
    }
    .dark .nv-editor__control select { background: var(--gray-800); border-color: var(--gray-600); color: var(--gray-100); }
    .nv-editor__devices { display: inline-flex; border-radius: .5rem; overflow: hidden; border: 1px solid var(--gray-300); }
    .dark .nv-editor__devices { border-color: var(--gray-600); }
    .nv-editor__devices button { padding: .35rem .7rem; font-size: .75rem; font-weight: 600; background: transparent; }
    .nv-editor__devices button.is-active { background: var(--primary-500); color: white; }
    .nv-editor__spacer { flex: 1; }
    .nv-editor__ghost {
        font-size: .75rem; font-weight: 600; padding: .35rem .7rem;
        border-radius: .5rem; border: 1px solid var(--gray-300);
    }
    .dark .nv-editor__ghost { border-color: var(--gray-600); }

    .nv-editor__body { display: flex; gap: .75rem; flex: 1; min-height: 0; }
    .nv-editor__stage {
        flex: 1; min-width: 0; overflow: auto; display: flex; justify-content: center;
        background: var(--gray-100); border-radius: .75rem; padding: .5rem;
    }
    .dark .nv-editor__stage { background: var(--gray-950); }
    .nv-editor__frame { height: 100%; transition: width .25s ease; }
    .nv-editor__frame iframe {
        width: 100%; height: 100%; border: 0; border-radius: .5rem; background: white;
        box-shadow: 0 10px 40px -20px rgba(0,0,0,.5);
    }

    .nv-editor__panel {
        width: 19rem; flex-shrink: 0; overflow: auto; padding: 1rem;
        border-radius: .75rem; background: white; border: 1px solid var(--gray-200);
    }
    .dark .nv-editor__panel { background: color-mix(in oklab, var(--gray-900) 70%, transparent); border-color: var(--gray-700); }
    .nv-editor__panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; }
    .nv-editor__panel-head h3 { font-size: 1rem; font-weight: 700; margin: .15rem 0 0; color: var(--gray-950); }
    .dark .nv-editor__panel-head h3 { color: var(--gray-50); }
    .nv-editor__kicker {
        font-size: .65rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: var(--gray-500);
    }
    .nv-editor__close { font-size: .9rem; color: var(--gray-500); }
    .nv-editor__locale { margin-top: .75rem; font-size: .75rem; color: var(--gray-500); }
    .nv-editor__status {
        margin-top: .625rem; padding: .5rem .625rem; border-radius: .5rem;
        font-size: .75rem; line-height: 1.5;
        background: var(--gray-50); color: var(--gray-500);
    }
    .dark .nv-editor__status { background: color-mix(in oklab, var(--gray-800) 60%, transparent); }
    .nv-editor__saved { font-weight: 600; color: var(--success-600); }
    .dark .nv-editor__saved { color: var(--success-400); }
    .nv-editor__hint { margin-top: .625rem; font-size: .72rem; color: var(--gray-500); line-height: 1.55; }
    .nv-editor__hint code { background: var(--gray-100); padding: 0 .2rem; border-radius: .2rem; }
    .dark .nv-editor__hint code { background: var(--gray-800); }

    /* The page outline: what is on this page, in the order it appears. */
    .nv-editor__sections { margin-top: .75rem; display: flex; flex-direction: column; gap: .125rem; }
    .nv-editor__section {
        display: flex; align-items: center; gap: .25rem;
        border-radius: .5rem; transition: background .12s;
    }
    .nv-editor__section:hover { background: var(--gray-50); }
    .dark .nv-editor__section:hover { background: color-mix(in oklab, var(--gray-800) 60%, transparent); }
    .nv-editor__section.is-hidden { opacity: .5; }
    .nv-editor__section-name {
        flex: 1; min-width: 0; display: flex; align-items: center; gap: .5rem;
        padding: .4rem .375rem; text-align: start; font-size: .75rem; line-height: 1.35;
        color: var(--gray-950);
    }
    .dark .nv-editor__section-name { color: var(--gray-100); }
    .nv-editor__section-name em { font-style: normal; font-size: .65rem; color: var(--gray-500); }
    .nv-editor__section-index {
        flex-shrink: 0; display: grid; place-items: center;
        width: 1.25rem; height: 1.25rem; border-radius: .25rem;
        font-size: .625rem; font-weight: 700;
        background: color-mix(in oklab, var(--primary-500) 14%, transparent);
        color: var(--primary-600);
    }
    .dark .nv-editor__section-index { color: var(--primary-400); }
    .nv-editor__section-tools { display: flex; gap: .0625rem; opacity: 0; transition: opacity .12s; }
    .nv-editor__section:hover .nv-editor__section-tools,
    .nv-editor__section:focus-within .nv-editor__section-tools { opacity: 1; }
    .nv-editor__section-tools button {
        width: 1.5rem; height: 1.5rem; border-radius: .3125rem;
        font-size: .7rem; color: var(--gray-500);
    }
    .nv-editor__section-tools button:hover:not(:disabled) { background: var(--gray-200); color: var(--gray-950); }
    .dark .nv-editor__section-tools button:hover:not(:disabled) { background: var(--gray-700); color: var(--gray-50); }
    .nv-editor__section-tools button:disabled { opacity: .25; cursor: default; }
    .nv-editor__add {
        display: block; margin-top: .75rem; padding: .5rem; border-radius: .5rem;
        border: 1px dashed var(--gray-300); text-align: center;
        font-size: .75rem; font-weight: 600; color: var(--primary-600);
    }
    .dark .nv-editor__add { border-color: var(--gray-600); color: var(--primary-400); }

    /* Picture chooser. */
    .nv-editor__media {
        margin-top: .625rem; display: grid; gap: .375rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .nv-editor__thumb {
        aspect-ratio: 4 / 3; border-radius: .375rem; overflow: hidden;
        border: 2px solid transparent; background: var(--gray-100);
    }
    .nv-editor__thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .nv-editor__thumb:hover { border-color: var(--primary-400); }
    .nv-editor__thumb.is-on { border-color: var(--primary-500); box-shadow: 0 0 0 2px var(--primary-500); }

    /* Below this the two columns are both too narrow to work in, so the panel
       moves above the preview instead of beside it. */
    @media (max-width: 1024px) {
        .nv-editor { height: auto; }
        .nv-editor__body { flex-direction: column-reverse; }
        .nv-editor__panel { width: auto; max-height: 24rem; }
        .nv-editor__stage { min-height: 32rem; }
    }

</style>

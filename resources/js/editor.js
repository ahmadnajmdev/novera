/**
 * Visual editor bridge (front-end half).
 *
 * Runs inside the CMS preview iframe. It never writes anything itself: it
 * reports what the editor typed and applies what the panel sends back, so the
 * public site keeps no save endpoint of its own.
 *
 * Text is edited in place. The panel used to own a textarea, which meant
 * clicking a headline, moving to the far side of the screen, typing, then
 * finding a Save button — three moves to change one word. Now the page itself
 * is the input and the panel only says what is happening.
 */
const CHANNEL = 'novera-editor';

function parseTarget(el) {
    const [model, id, field, locale] = (el.getAttribute('data-nv-edit') || '').split(':');
    return model && id && field ? { model, id, field, locale } : null;
}

function parseMediaTarget(el) {
    const [model, id, field] = (el.getAttribute('data-nv-media') || '').split(':');
    return model && id && field ? { model, id, field } : null;
}

function post(type, payload = {}) {
    window.parent?.postMessage({ channel: CHANNEL, type, ...payload }, window.location.origin);
}

/**
 * Turn the rendered node back into what is stored.
 *
 * Headings keep their gold fragment as *asterisks* in the database and render
 * it as <em class="nv-accent">, so the markers have to be put back or every
 * inline edit would quietly strip the accent.
 */
function serialise(el) {
    let out = '';

    el.childNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            out += node.textContent;
            return;
        }

        if (node.nodeType !== Node.ELEMENT_NODE) return;

        if (node.matches('em.nv-accent, em, i')) {
            const inner = node.textContent.trim();
            out += inner ? `*${inner}*` : '';
            return;
        }

        if (node.tagName === 'BR') {
            out += '\n';
            return;
        }

        out += serialise(node);
    });

    return out.replace(/ /g, ' ').replace(/[ \t]+\n/g, '\n').trim();
}

let active = null;
let committedValue = null;

function commit(el) {
    const target = parseTarget(el);
    if (!target) return;

    const value = serialise(el);
    if (value === committedValue) return;

    committedValue = value;
    post('commit', { ...target, value });
}

function focusNode(el) {
    if (active === el) return;

    active?.classList.remove('is-selected');
    active = el;
    el.classList.add('is-selected');
    committedValue = serialise(el);

    post('select', { ...parseTarget(el), label: el.tagName.toLowerCase() });
}

function initTextEditing() {
    document.querySelectorAll('[data-nv-edit]').forEach((el) => {
        el.setAttribute('contenteditable', 'true');
        el.setAttribute('spellcheck', 'true');
        el.setAttribute('role', 'textbox');

        el.addEventListener('focus', () => focusNode(el));
        el.addEventListener('blur', () => commit(el));

        el.addEventListener('keydown', (event) => {
            // Enter finishes the edit rather than splitting the element into
            // markup the theme has no way to render.
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                el.blur();
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                el.blur();
            }

            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 's') {
                event.preventDefault();
                commit(el);
            }
        });

        // Pasting rich text from a document would drag its markup in with it.
        el.addEventListener('paste', (event) => {
            event.preventDefault();
            const text = event.clipboardData?.getData('text/plain') ?? '';
            document.execCommand('insertText', false, text.replace(/\s+/g, ' '));
        });
    });
}

function initMediaEditing() {
    document.querySelectorAll('[data-nv-media]').forEach((el) => {
        el.classList.add('nv-editable-media');
        el.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const target = parseMediaTarget(el);
            if (target) post('pick-media', target);
        });
    });
}

function initNavigationGuard() {
    // Links must not navigate while an editor is working on the page.
    document.addEventListener(
        'click',
        (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;
            event.preventDefault();
        },
        true,
    );

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => event.preventDefault());
    });
}

function applyText({ model, id, field, html, text }) {
    const selector = `[data-nv-edit^="${model}:${id}:${field}:"]`;
    document.querySelectorAll(selector).forEach((el) => {
        if (el === active && document.activeElement === el) return;
        if (html !== undefined && html !== null) {
            el.innerHTML = html;
        } else {
            el.textContent = text ?? '';
        }
    });
    document.dispatchEvent(new CustomEvent('nv:content-updated'));
}

function applyMedia({ model, id, field, url }) {
    document.querySelectorAll(`[data-nv-media="${model}:${id}:${field}"]`).forEach((el) => {
        if (el.tagName === 'IMG') el.src = url ?? '';
        else el.style.backgroundImage = url ? `url(${url})` : '';
    });
}

window.addEventListener('message', (event) => {
    if (event.origin !== window.location.origin) return;
    const data = event.data;
    if (!data || data.channel !== CHANNEL) return;

    if (data.type === 'apply') applyText(data);
    if (data.type === 'apply-media') applyMedia(data);
    if (data.type === 'deselect') {
        active?.classList.remove('is-selected');
        active = null;
    }
    if (data.type === 'focus') {
        const el = document.querySelector(`[data-nv-edit^="${data.model}:${data.id}:${data.field}:"]`);
        el?.scrollIntoView({ block: 'center', behavior: 'smooth' });
        el?.focus();
    }
    if (data.type === 'scroll-to-section') {
        const el = document.querySelector(`[data-nv-section="${data.id}"]`);
        el?.scrollIntoView({ block: 'start', behavior: 'smooth' });
        el?.classList.add('is-flashed');
        setTimeout(() => el?.classList.remove('is-flashed'), 1200);
    }
});

function boot() {
    // The layout already sets this; repeated here so the affordances
    // survive a preview rendered without the query parameter.
    document.body.classList.add('nv-editing');
    initTextEditing();
    initMediaEditing();
    initNavigationGuard();
    post('ready', { fields: document.querySelectorAll('[data-nv-edit]').length });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

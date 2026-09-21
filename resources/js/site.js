/**
 * Novera Interiors — theme behaviour.
 *
 * Every effect reads its on/off switch from `window.NoveraMotion`, which the
 * layout writes from CMS settings, so an editor can disable any of it without
 * a rebuild. Everything also no-ops under prefers-reduced-motion.
 */
const motion = Object.assign(
    { reveal: true, parallax: true, smoothScroll: true, railAutoscroll: true },
    window.NoveraMotion || {},
);

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ── Scroll reveal ────────────────────────────────────────────────────── */
function initReveal() {
    if (!motion.reveal || reduced) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'none';
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.05, rootMargin: '0px 0px -4% 0px' },
    );

    const arm = () => {
        document.querySelectorAll('[data-reveal]:not([data-armed])').forEach((el) => {
            el.setAttribute('data-armed', '');
            if (el.getBoundingClientRect().top <= window.innerHeight * 0.92) return;
            el.style.opacity = '0';
            el.style.transform = 'translateY(44px) scale(.985)';
            observer.observe(el);
        });
    };

    arm();
    [250, 800, 1800].forEach((delay) => setTimeout(arm, delay));
    document.addEventListener('livewire:navigated', arm);
    document.addEventListener('nv:content-updated', arm);
}

/* ── Parallax + progress + nav state ──────────────────────────────────── */
function initScrollEffects() {
    const nav = document.querySelector('[data-nav]');
    const bar = document.querySelector('[data-progress] span');
    let scrolled = null;

    const onScroll = () => {
        const y = window.scrollY || document.documentElement.scrollTop || 0;

        if (nav) {
            const next = y > 40;
            if (next !== scrolled) {
                scrolled = next;
                nav.classList.toggle('is-solid', next || nav.dataset.navSolid === '1');
            }
        }

        if (bar) {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.width = (max > 0 ? Math.min(100, (y / max) * 100) : 0) + '%';
        }

        if (!motion.parallax || reduced) return;

        const vh = window.innerHeight;
        document.querySelectorAll('[data-parallax]').forEach((el) => {
            const rect = el.getBoundingClientRect();
            if (rect.bottom < -300 || rect.top > vh + 300) return;
            const factor = parseFloat(el.getAttribute('data-parallax')) || 0.04;
            const shift = (rect.top + rect.height / 2 - vh / 2) * factor;
            el.style.transform = `translate3d(0, ${(-shift).toFixed(1)}px, 0)`;
        });
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    onScroll();
}

/* ── Concept rail auto-scroll ─────────────────────────────────────────── */
function initRail() {
    const rail = document.querySelector('[data-rail]');
    if (!rail || !motion.railAutoscroll || reduced) return;

    let position = 0;

    const tick = () => {
        if (!rail.isConnected) return;
        if (!rail.matches(':hover')) {
            // The track renders the concept list twice; wrap at the halfway
            // point so the loop is seamless.
            const half = rail.scrollWidth / 2;
            if (half > 0) {
                position += 0.55;
                if (position >= half) position -= half;
                rail.scrollLeft = position;
            }
        }
        requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
}

/* ── Eased wheel scrolling ────────────────────────────────────────────── */
function initSmoothScroll() {
    if (!motion.smoothScroll || reduced) return;

    let target = 0;
    let current = 0;
    let easing = false;

    window.addEventListener(
        'wheel',
        (event) => {
            if (event.ctrlKey || event.metaKey) return;
            if (event.target?.closest?.('[data-rail], textarea, select, [contenteditable="true"]')) return;

            const max = document.documentElement.scrollHeight - window.innerHeight;
            if (max <= 0) return;

            event.preventDefault();
            if (!easing) current = window.scrollY || 0;
            target = Math.max(0, Math.min(max, (easing ? target : current) + event.deltaY * 1.15));
            easing = true;
        },
        { passive: false },
    );

    const tick = () => {
        if (easing) {
            const delta = target - current;
            if (Math.abs(delta) < 0.5) {
                current = target;
                easing = false;
            } else {
                current += delta * 0.085;
            }
            window.scrollTo(0, current);
        }
        requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
}

/* ── Hero video ───────────────────────────────────────────────────────── */
function initHeroVideo() {
    const video = document.querySelector('[data-hero-video]');
    if (!video) return;

    video.muted = true;
    video.loop = true;
    video.playsInline = true;
    const play = () => video.play?.()?.catch?.(() => {});
    play();
    [200, 900].forEach((delay) => setTimeout(play, delay));
}

/**
 * The splash covers the whole viewport at z-index 200. Relying on the CSS
 * animation alone to hide it means any environment that does not run
 * animations leaves the site unusable, so remove the node outright.
 */
function initBootSplash() {
    const splash = document.querySelector('[data-boot]');
    if (!splash) return;

    const remove = () => splash.remove();

    if (reduced) {
        remove();
        return;
    }

    splash.addEventListener('animationend', remove);
    setTimeout(remove, 3000);
}

function boot() {
    initBootSplash();
    initReveal();
    initScrollEffects();
    initRail();
    initSmoothScroll();
    initHeroVideo();
}

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:navigated', () => {
    initScrollEffects();
    initRail();
    initHeroVideo();
});

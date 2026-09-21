# Novera Interiors

The Novera Interiors website and its CMS. Built from the Claude Design canvas
`Novera Interiors v2.dc.html`, in English, Kurdish (Sorani) and Arabic.

Nothing on the public site is hard-coded. Every heading, paragraph, image,
colour, menu link, filter chip, form field and URL slug is a database row that
an editor can change.

## Stack

| Component | Version |
| --- | --- |
| Laravel | 13.30 |
| Livewire | 4.4 |
| Alpine.js | bundled with Livewire 4 |
| Filament | 5.7 |
| PHP | 8.4 |

## Getting started

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build          # or: npm run dev
```

The seed loads the whole design as content: 7 pages, 21 blocks, 10 concepts,
14 materials, 9 projects, 4 services, 8 content lists and 467 translation
entries.

Sign in at `/admin` with `admin@noverainteriors.com` / `password`.
**Change that password before deploying.**

Development runs on SQLite. For MySQL/MariaDB, point `DB_*` at your database
and re-run `php artisan migrate --seed`.

## How the site is put together

### URLs are content

There is one route. `PageController::resolve()` walks the request path against
the active locale codes and the translated slugs stored on each page, so
renaming a page in the CMS moves its URL in every language without a code
change.

```
/{locale}                     → the page keyed "home"
/{locale}/{page-slug}         → any page
/{locale}/{page-slug}/{item}  → a concept, project or material detail
```

Which index pages own a detail route is configured in
`config/novera.php → detail_pages`.

### Pages are sections

A page is an ordered list of typed sections (`sections` table) — the CMS calls
them sections; the code calls the registry `SectionBlocks`. Each type is
declared once in `App\Filament\Support\SectionBlocks` and rendered by a
matching Blade view in `resources/views/site/sections`.

To add a section type:

1. Add an entry to `SectionBlocks::definitions()` with a `hint` describing
   what it looks like, a `group` heading and an `icon` — the picker is a grid
   of cards, not a dropdown, so all three are required.
2. Create `resources/views/site/sections/<type-with-dashes>.blade.php`, marking
   every wording field with `nv_edit()` and every picture with
   `nv_media_edit()`.

The CMS form is derived from the definition — there is no per-type form to
write. Declare button destinations under `pages` (not `plain`) so they render
as a page picker rather than asking for a key.

### Headings and the gold accent

Headings store their italic gold fragment with asterisks:

```
Crafting spaces that define *living*
```

`nv_accent()` splits on the marker and translates each fragment separately.
That is deliberate: the translation dictionary is keyed by exactly these
fragments, because the design's markup put them in separate text nodes.

### Translation

Two mechanisms, used together:

- **Per-record translations.** Content models store translatable columns as
  JSON via `spatie/laravel-translatable` (`name`, `slug`, `body`, …). The CMS
  renders one tab per active locale.
- **The dictionary** (`translations` table). Copy authored in English is
  translated by matching the source string. Generated strings — "Ideas we
  return to for the kitchen" — resolve through regex patterns that substitute
  the room name, so they need no entry per concept.

`nv_tr()` reads a model attribute for the active locale and falls back to the
source language *through the dictionary*, so partially translated content
still reads correctly.

Adding a language is a row in **Languages → Languages**. It immediately
becomes routable and adds a tab to every content form.

### Design tokens

Colours, radius, container width and font stacks are settings, written into
`:root` custom properties on each request. Editing the palette in
**Settings → Site settings → Colours & type** changes the site without a rebuild.

Motion (scroll reveal, parallax, eased scrolling, marquee, boot splash, rail
auto-scroll) is individually switchable, and all of it respects
`prefers-reduced-motion`.

## The visual editor

**Website → Visual editor** renders the live site in a same-origin iframe with
`?nv-edit=1`.

Text is edited in place. Every marked node becomes `contenteditable`, so you
type on the page itself and it saves when you click away — no side panel, no
save button. Headings are serialised back to their `*asterisk*` form on the
way out, so an inline edit never strips the gold accent. Pictures are clicked
to open a chooser. The right-hand panel shows the page's sections, which can
be reordered and hidden from there.

The preview frame never writes anything. It reports what was typed; the panel
saves through the authenticated admin panel and pushes the re-rendered
fragment back. Writes are confined to allowlists in
`App\Support\InlineEditor` — `SECTION_FIELDS` for wording,
`SECTION_MEDIA_FIELDS` for pictures — and every save is recorded in
`revisions`.

Edit mode requires an authenticated user — it only changes rendering, so it
exposes no endpoint of its own.

A wording field the theme never marks with `nv_edit()` can only be reached
through the admin form, which is the detour the editor exists to remove.
`EditingFlowsTest` fails if a registry field has no marker.

## Written for people who do not write software

The CMS is used by an office, not by developers, and several decisions exist
only to keep it that way. They are easy to undo by accident, so
`tests/Feature/CmsUsabilityTest.php` holds them in place:

- **Nothing asks for an identifier.** `key` is generated from the name by
  `App\Models\Concerns\HasAutoKey` and only appears inside a collapsed
  *Advanced* drawer (`App\Filament\Support\Advanced`).
- **Destinations are picked, never typed.** Buttons choose a page from a list;
  sections choose their list and form the same way.
- **The sidebar is in plain language,** grouped as Website / Media & lists /
  Menus & forms, with the rarely-used groups collapsed.
- **The dashboard is a set of jobs**, not an empty page — see
  `App\Filament\Widgets\StartHere`.
- **Every list screen says what it is for** via `getSubheading()`.
- **Adding a page is a three-question wizard** (`CreatePage`) that builds the
  sections from a starting layout in `App\Filament\Support\PageLayouts`, adds
  the menu links and publishes — so what you land on is a finished page, not an
  empty one.
- **Adding a section is a card picker**, and “Add above” inserts one between
  two existing sections rather than only at the end.

When adding a field, write the label as a sentence an office manager would
say out loud, and put the plumbing in the Advanced drawer.

## Roles

- **Administrator** — everything, including languages, users, redirects, forms
  and site settings.
- **Editor** — content only.

## Caching

`Settings`, `SiteTranslator` and page slugs are cached; `Site` memoises menus,
locales and content lists per request. `App\Support\CacheFlusher` invalidates
them on save, which also keeps a long-lived container (Octane) honest.

## Tests

```bash
php artisan test
```

40 tests / 200 assertions covering: every page in every locale, RTL, the
dictionary and its regex patterns, drafts, redirects, every Filament screen,
the visual editor including its write allowlist, dynamic forms and filters,
role gating, and cache invalidation.

## Where the design lives

`design/` holds the source canvas, its brand assets and the original
`novera-i18n.js`, kept for reference. Brand marks are in the media library;
fonts (Cinzel, Cinzel Decorative — SIL OFL — and the Novera Arabic face) are
self-hosted from `public/fonts/novera`.

## Before going live

- Change the seeded admin password.
- Replace the Unsplash/Pexels placeholders (Library → Media) with Novera's own
  photography. Every one is a media row; no template references an image
  directly.
- Set `APP_ENV=production`, `APP_DEBUG=false` and a real `MAIL_*` config so
  enquiry notifications send.
- Point `hero.video_url` at self-hosted footage rather than the Pexels sample.

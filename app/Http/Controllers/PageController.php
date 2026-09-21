<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\Material;
use App\Models\Page;
use App\Models\Project;
use App\Models\Redirect;
use App\Support\Site;
use App\Support\Urls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    public function __construct(
        protected Site $site,
        protected Urls $urls,
    ) {}

    /**
     * Single entry point for the whole public site.
     *
     * Paths are data, not route definitions, so resolution walks the request
     * segments against locale codes and translated slugs rather than matching
     * hard-coded URI patterns.
     */
    public function resolve(Request $request, string $path = ''): Response
    {
        if ($redirect = $this->matchRedirect($path)) {
            return redirect($redirect->to, $redirect->status);
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        $codes = $this->site->locales()->pluck('code')->all();
        $default = $this->site->defaultLocale()?->code ?? 'en';

        // A missing locale prefix is a canonical-URL problem, not a 404.
        if ($segments === [] || ! in_array($segments[0], $codes, true)) {
            return redirect('/'.$default.($segments ? '/'.implode('/', $segments) : ''), 302);
        }

        $locale = array_shift($segments);
        app()->setLocale($locale);

        return match (count($segments)) {
            0 => $this->renderPage($request, $this->findPage('home', $locale), $locale),
            1 => $this->renderPage($request, $this->findPageBySlug($segments[0], $locale), $locale),
            2 => $this->renderDetail($request, $segments[0], $segments[1], $locale),
            default => abort(404),
        };
    }

    protected function matchRedirect(string $path): ?Redirect
    {
        $normalised = '/'.trim($path, '/');

        $rows = Cache::rememberForever(
            'novera.redirects',
            fn () => Redirect::where('is_active', true)->get(['from', 'to', 'status'])->toArray(),
        );

        foreach ($rows as $row) {
            if (rtrim($row['from'], '/') === rtrim($normalised, '/')) {
                return new Redirect($row);
            }
        }

        return null;
    }

    protected function findPage(string $key, string $locale): Page
    {
        $page = Page::with('sections')->where('key', $key)->first();

        abort_if($page === null || ! $this->viewable($page), 404);

        return $page;
    }

    protected function findPageBySlug(string $slug, string $locale): Page
    {
        $page = Page::with('sections')->get()->first(
            fn (Page $candidate) => $candidate->getTranslation('slug', $locale) === $slug
                || in_array($slug, array_values($candidate->getTranslations('slug')), true),
        );

        abort_if($page === null || ! $this->viewable($page), 404);

        return $page;
    }

    /** Drafts stay reachable for signed-in editors so previews work. */
    protected function viewable(Page $page): bool
    {
        return $page->isPublished() || auth()->check();
    }

    protected function renderPage(Request $request, Page $page, string $locale): Response
    {
        $request->attributes->set('nv.page', $page);

        return response()->view('site.page', [
            'page' => $page,
            'locale' => $locale,
        ]);
    }

    protected function renderDetail(Request $request, string $parentSlug, string $childSlug, string $locale): Response
    {
        $parent = $this->findPageBySlug($parentSlug, $locale);
        $modelClass = config('novera.detail_pages')[$parent->key] ?? null;

        abort_if($modelClass === null, 404);

        $model = $this->findBySlug($modelClass, $childSlug, $locale);

        abort_if($model === null, 404);

        $request->attributes->set('nv.page', $parent);
        $request->attributes->set('nv.entity', $model);

        return match ($modelClass) {
            Concept::class => $this->conceptView($model, $parent, $locale),
            Project::class => $this->projectView($model, $parent, $locale),
            Material::class => $this->materialView($model, $parent, $locale),
            default => abort(404),
        };
    }

    protected function findBySlug(string $modelClass, string $slug, string $locale): ?Model
    {
        return $modelClass::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get()
            ->first(fn (Model $model) => $model->getTranslation('slug', $locale) === $slug
                || $model->key === $slug);
    }

    protected function conceptView(Concept $concept, Page $parent, string $locale): Response
    {
        $siblings = Concept::active()->get();
        $index = $siblings->search(fn (Concept $item) => $item->is($concept));

        return response()->view('site.concept', [
            'page' => $parent,
            'locale' => $locale,
            'concept' => $concept->load('media', 'items.media'),
            'previous' => $siblings[($index - 1 + $siblings->count()) % $siblings->count()],
            'next' => $siblings[($index + 1) % $siblings->count()],
        ]);
    }

    protected function projectView(Project $project, Page $parent, string $locale): Response
    {
        $siblings = Project::active()->get();
        $index = $siblings->search(fn (Project $item) => $item->is($project));

        return response()->view('site.project', [
            'page' => $parent,
            'locale' => $locale,
            'project' => $project->load('media', 'wideMedia', 'images.media', 'materials', 'concepts', 'category', 'status'),
            'previous' => $siblings[($index - 1 + $siblings->count()) % $siblings->count()],
            'next' => $siblings[($index + 1) % $siblings->count()],
        ]);
    }

    protected function materialView(Material $material, Page $parent, string $locale): Response
    {
        return response()->view('site.material', [
            'page' => $parent,
            'locale' => $locale,
            'material' => $material->load('media', 'group', 'specs', 'images.media', 'concepts'),
        ]);
    }
}

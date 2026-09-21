<?php

namespace App\Http\Middleware;

use App\Support\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(protected Site $site) {}

    public function handle(Request $request, Closure $next): Response
    {
        $codes = $this->site->locales()->pluck('code')->all();
        $default = $this->site->defaultLocale()?->code ?? 'en';

        $locale = $request->route('locale');

        if (! in_array($locale, $codes, true)) {
            $locale = $default;
        }

        app()->setLocale($locale);
        view()->share('locale', $locale);
        view()->share('direction', $this->site->direction($locale));

        return $next($request);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSystemLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = localeAndRegion();
        setlocale(LC_ALL, $locale . '.utf8', $locale . '.utf-8', $locale . '.UTF-8', $locale);

        return $next($request);
    }
}

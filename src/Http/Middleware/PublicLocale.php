<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PublicLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $firstSegment = $request->segment(1);

        if (!in_array($firstSegment, config('translatable.locales'))) {
            return $next($request);
        }

        $locale = $firstSegment;

        App::setlocale($locale);

        // Not very reliable, need to be refactored
        $localeAndCountry = $locale.'_'.strtoupper($locale);

        setlocale(LC_ALL, $localeAndCountry.'.utf8', $localeAndCountry.'.utf-8', $localeAndCountry);

        // Throw a 404 if website in this language is offline
        if (!config('typicms.'.$locale.'.status')) {
            abort(404);
        }

        // Remove preview param if no admin user connected
        if ($request->input('preview') && !Auth::check()) {
            return Redirect::to($request->path());
        }

        return $next($request);
    }
}

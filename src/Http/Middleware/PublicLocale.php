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

        if (in_array($firstSegment, config('translatable.locales'))) {
            $locale = $firstSegment;
        } else {
            $locale = config('app.fallback_locale');
        }

        App::setlocale($locale);

        // Not very reliable, need to be refactored
        $combinedLocale = $locale.'_'.strtoupper($locale);

        setlocale(LC_ALL, $combinedLocale.'.utf8', $combinedLocale.'.utf-8', $combinedLocale);

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

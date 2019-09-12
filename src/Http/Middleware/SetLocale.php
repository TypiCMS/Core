<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = config('app.locale');
        $firstSegment = $request->segment(1);
        if (in_array($firstSegment, locales())) {
            $locale = $firstSegment;
            App::setLocale($locale);
        }

        // Add locale prefix to URL if required.
        if (
            $firstSegment !== 'admin' &&
            !in_array($firstSegment, locales()) &&
            config('typicms.main_locale_in_url')
        ) {
            $segments = $request->segments();
            $segments = Arr::prepend($segments, TypiCMS::mainLocale());
            return redirect()->to(implode('/', $segments));
        }

        // Not reliable, to be refactored.
        $localeAndCountry = $locale.'_'.strtoupper($locale);

        setlocale(LC_ALL, $localeAndCountry.'.utf8', $localeAndCountry.'.utf-8', $localeAndCountry.'.UTF-8', $localeAndCountry);

        // Throw a 404 if website in this language is offline
        if ($firstSegment !== 'admin' && !config('typicms.'.$locale.'.status')) {
            abort(404);
        }

        // Remove preview param if no admin user connected
        if ($request->input('preview') && !Auth::check()) {
            return Redirect::to($request->path());
        }

        return $next($request);
    }
}

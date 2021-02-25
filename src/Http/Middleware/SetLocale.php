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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = App::getLocale();
        $firstSegment = $request->segment(1);
        if (in_array($firstSegment, TypiCMS::enabledLocales())) {
            $locale = $firstSegment;
            App::setLocale($locale);
        }

        // Add locale prefix to URL if required.
        if (
            $firstSegment !== 'admin' &&
            !in_array($firstSegment, TypiCMS::enabledLocales()) &&
            config('typicms.main_locale_in_url')
        ) {
            $segments = $request->segments();
            $segments = Arr::prepend($segments, $this->getBrowserLanguageOrDefault());

            return redirect()->to(implode('/', $segments));
        }

        // Not reliable, to be refactored.
        $localeAndCountry = $locale.'_'.mb_strtoupper($locale);

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

    private function getBrowserLanguageOrDefault()
    {
        if ($browserLanguage = getenv('HTTP_ACCEPT_LANGUAGE')) {
            $browserLocale = mb_substr($browserLanguage, 0, 2);
            if (in_array($browserLocale, TypiCMS::enabledLocales())) {
                return $browserLocale;
            }
        }

        return TypiCMS::mainLocale();
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->getLocaleFromDomainName();
        $firstSegment = $request->segment(1);

        if (!$locale) {
            $locale = config('app.locale');
            if (in_array($firstSegment, config('translatable.locales'))) {
                $locale = $firstSegment;
            }
        }

        App::setlocale($locale);

        // Not very reliable, need to be refactored
        $localeAndCountry = $locale.'_'.strtoupper($locale);

        setlocale(LC_ALL, $localeAndCountry.'.utf8', $localeAndCountry.'.utf-8', $localeAndCountry);

        // Throw a 404 if website in this language is offline
        if ($firstSegment != 'admin' && !config('typicms.'.$locale.'.status')) {
            abort(404);
        }

        // Remove preview param if no admin user connected
        if ($request->input('preview') && !Auth::check()) {
            return Redirect::to($request->path());
        }

        return $next($request);
    }

    /**
     * Get the front office locale.
     *
     * @return string|false
     */
    private function getLocaleFromDomainName()
    {
        $baseUrl = explode('/', url('/'));
        $domainName = end($baseUrl);
        $domainNames = [];
        foreach (config('translatable.locales') as $locale) {
            $domainNames[$locale] = config('typicms.translated_domains.'.$locale);
        }

        $found = array_keys($domainNames, $domainName);
        if (count($found) == 1) {
            return reset($found);
        }

        return false;
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class AddLocaleToRootUrl
{
    public function handle(Request $request, Closure $next)
    {
        // Add locale prefix to root URL if required.
        if (
            $request->segment(1) === null &&
            !config('typicms.lang_chooser') &&
            config('typicms.main_locale_in_url')
        ) {
            return redirect($this->getBrowserLanguageOrDefault());
        }

        return $next($request);
    }

    private function getBrowserLanguageOrDefault(): string
    {
        if ($locale = mb_substr(getenv('HTTP_ACCEPT_LANGUAGE'), 0, 2)) {
            if (in_array($locale, TypiCMS::enabledLocales())) {
                return $locale;
            }
        }

        return TypiCMS::mainLocale();
    }
}

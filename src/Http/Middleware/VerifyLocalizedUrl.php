<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyLocalizedUrl
{
    public function handle(Request $request, Closure $next): mixed
    {
        // If config(typicms.main_locale_in_url) is set to true,
        // the URLs without locale in it should throw a 404 error.
        if (
            config('typicms.main_locale_in_url')
            && $request->segment(1) !== null
            && !in_array($request->segment(1), enabledLocales())
        ) {
            abort(404);
        }

        return $next($request);
    }
}

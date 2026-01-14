<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = mainLocale();
        $firstSegment = $request->segment(1);
        if (in_array($firstSegment, enabledLocales(), true)) {
            $locale = $firstSegment;
        }

        if (!in_array($locale, enabledLocales(), true)) {
            abort(404);
        }

        App::setLocale($locale);
        Date::setLocale(localeAndRegion());
        Number::useLocale(localeAndRegion());

        return $next($request);
    }
}

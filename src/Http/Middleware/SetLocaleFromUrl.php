<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
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
        if (in_array($firstSegment, enabledLocales())) {
            $locale = $firstSegment;
        }
        App::setLocale($locale);
        Carbon::setLocale(localeAndRegion());
        Number::useLocale(localeAndRegion());

        return $next($request);
    }
}

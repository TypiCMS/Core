<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $firstSegment = $request->segment(1);
        if (in_array($firstSegment, enabledLocales())) {
            App::setLocale($firstSegment);
        }
        Carbon::setLocale(localeAndRegion());

        return $next($request);
    }
}

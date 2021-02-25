<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use TypiCMS\Modules\Core\Facades\TypiCMS;

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
        if (in_array($firstSegment, TypiCMS::enabledLocales())) {
            App::setLocale($firstSegment);
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetNavbarLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $navbarLocale = app()->getLocale();
        if (auth()->check()) {
            $userLocale = auth()->user()->locale;
            if (in_array($userLocale, locales(), true)) {
                $navbarLocale = $userLocale;
            }
        }

        config(['typicms.navbar_locale' => $navbarLocale]);

        return $next($request);
    }
}

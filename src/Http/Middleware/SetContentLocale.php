<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetContentLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Store requested locale in session.
        $localeFromRequest = $request->input('locale');
        if (in_array($localeFromRequest, locales())) {
            session(['locale' => $localeFromRequest]);
        }

        // Set content locale.
        $localeFromSession = session('locale', config('app.locale'));
        if (in_array($localeFromSession, locales())) {
            config(['typicms.content_locale' => $localeFromSession]);
        }

        return $next($request);
    }
}

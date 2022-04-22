<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminLocale
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

        // Set admin interface locale.
        $userLocale = auth()->user()->locale;
        if (in_array($userLocale, locales())) {
            app()->setLocale($userLocale);
            config(['typicms.admin_locale' => $userLocale]);
        }

        // Set content locale.
        $localeFromSession = session('locale', config('app.locale'));
        if (in_array($localeFromSession, locales())) {
            config(['typicms.content_locale' => $localeFromSession]);
        }

        return $next($request);
    }
}

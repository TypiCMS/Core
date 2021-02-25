<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class AdminLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->input('locale');

        // If requested locale is present in app.locales, store locale in session.
        if (in_array($locale, locales())) {
            $request->session()->put('locale', $locale);
        }

        // Set app.locale to user locale or value in config
        $adminLocale = auth()->user()->locale ?? config('typicms.admin_locale');
        App::setLocale($adminLocale);
        config(['typicms.admin_locale' => $adminLocale]);

        // Set translatable locale to locale
        // Don’t set translatable locale if locale is 'all'
        $localeInSession = $request->session()->get('locale', config('app.locale'));
        if (in_array($localeInSession, locales())) {
            Config::set('typicms.content_locale', $localeInSession);
        }

        return $next($request);
    }
}

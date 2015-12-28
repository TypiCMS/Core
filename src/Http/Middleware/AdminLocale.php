<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class AdminLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->input('locale');

        // If requested locale is present in app.locales, store locale in session.
        if (in_array($locale, config('translatable.locales'))) {
            $request->session()->put('locale', $locale);
        }

        // Set app.locale to admin_locale
        app()->setLocale(config('typicms.admin_locale'));

        // Set translatable locale to locale
        Config::set('translatable.locale', $request->session()->get('locale', config('app.locale')));

        return $next($request);
    }
}

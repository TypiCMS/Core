<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
            Session::put('locale', $locale);
        }

        // Set app.locale to admin_locale
        App::setLocale(config('typicms.admin_locale'));

        // Set translatable locale to locale
        Config::set('translatable.locale', Session::get('locale', config('app.locale')));

        return $next($request);
    }
}

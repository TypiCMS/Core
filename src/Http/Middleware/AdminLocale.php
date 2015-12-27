<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
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
        // If locale is present in app.locales…
        if (in_array(Input::get('locale'), config('translatable.locales'))) {
            // …store locale in session
            Session::put('locale', Input::get('locale'));
        }

        // Set app.locale to admin_locale
        App::setLocale(config('typicms.admin_locale'));

        // Set translatable locale to locale
        Config::set('translatable.locale', Session::get('locale', config('app.locale')));

        return $next($request);
    }
}

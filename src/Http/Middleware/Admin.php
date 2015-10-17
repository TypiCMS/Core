<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use JavaScript;

class Admin
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
        $locales = config('translatable.locales');

        // If locale is present in app.locales…
        if (in_array(Input::get('locale'), $locales)) {
            // …store locale in session
            Session::put('locale', Input::get('locale'));
        }

        // Set app.locale
        App::setLocale(config('typicms.admin_locale'));

        // Set Translator locale to typicms.admin_locale config
        Config::set('translatable.locale', Session::get('locale', config('app.locale')));

        $localesForJS = [];
        foreach ($locales as $key => $locale) {
            $localesForJS[] = [
                'short' => $locale,
                'long'  => trans('global.languages.'.$locale),
            ];
        }

        // Set Locales to JS.
        JavaScript::put([
            '_token'          => csrf_token(),
            'encrypted_token' => Crypt::encrypt(csrf_token()),
            'locales'         => $localesForJS,
        ]);

        // set curent user preferences to Config
        if ($request->user()) {
            $prefs = $request->user()->preferences;
            Config::set('typicms.user', $prefs);
        }

        return $next($request);
    }
}

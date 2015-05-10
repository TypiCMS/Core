<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Config;
use Crypt;
use Input;
use JavaScript;
use Lang;
use Session;
use Users;

class Admin
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale      = config('app.locale');
        $adminLocale = config('typicms.admin_locale');
        $locales     = config('translatable.locales');
        // If locale is present in app.locales…
        if (in_array(Input::get('locale'), $locales)) {
            // …store locale in session
            Session::put('locale', Input::get('locale'));
        }
        // Set app.locale
        Config::set('app.locale', Session::get('locale', $locale));
        // Set Translator locale to typicms.admin_locale config
        Lang::setLocale($adminLocale);

        $localesForJS = [];
        foreach ($locales as $key => $locale) {
            $localesForJS[] = [
                'short' => $locale,
                'long' => trans('global.languages.' . $locale)
            ];
        }
        // Set Locales to JS.
        JavaScript::put([
            '_token'          => csrf_token(),
            'encrypted_token' => Crypt::encrypt(csrf_token()),
            'adminLocale'     => $adminLocale,
            'locales'         => $localesForJS,
            'locale'          => config('app.locale'),
        ]);

        // set curent user preferences to Config
        $prefs = Users::getPreferences();
        Config::set('typicms.user', $prefs);

        return $next($request);
    }

}

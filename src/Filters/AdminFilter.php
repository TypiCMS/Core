<?php
namespace TypiCMS\Filters;

use Lang;
use Input;
use Config;
use Session;
use JavaScript;

/**
* Public filter
*/
class AdminFilter
{

    // Set App and Translator locale
    public function setLocale()
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
            'adminLocale' => $adminLocale,
            'locales'     => $localesForJS,
            'locale'      => config('app.locale'),
        ]);
    }
}

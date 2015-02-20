<?php namespace TypiCMS\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class LocaleComposer
{
    public function compose(View $view)
    {
        $currentLocale = App::getLocale();
        $view->with('lang', $currentLocale);
        $view->with('locale', $currentLocale);
        $view->with('locales', config('translatable.locales'));
    }
}

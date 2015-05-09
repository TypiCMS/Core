<?php namespace TypiCMS\Composers;

use Illuminate\Contracts\View\View;

class LocalesComposer
{
    public function compose(View $view)
    {
        $view->with('locales', config('translatable.locales'));
        $view->with('locale', config('app.locale'));
    }
}

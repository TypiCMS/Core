<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class LocalesComposer
{
    /*
     * For back end forms
     */
    public function compose(View $view)
    {
        $view->with('locales', locales());
        $view->with('locale', config('typicms.content_locale'));
    }
}

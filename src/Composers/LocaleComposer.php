<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class LocaleComposer
{
    /*
     * For front end
     */
    public function compose(View $view)
    {
        $view->with('lang', config('app.locale'));
    }
}

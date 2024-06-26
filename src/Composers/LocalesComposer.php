<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class LocalesComposer
{
    /*
     * For back end forms
     */
    public function compose(View $view): void
    {
        $view->with('locales', locales());
    }
}

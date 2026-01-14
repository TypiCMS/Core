<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class LocaleComposer
{
    /*
     * For front end
     */
    public function compose(View $view): void
    {
        $view->with('lang', app()->getLocale());
    }
}

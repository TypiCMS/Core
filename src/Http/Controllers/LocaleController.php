<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LocaleController extends BaseAdminController
{
    /**
     * Change content locale.
     *
     * @return void
     */
    public function setContentLocale($locale)
    {
        Session::put('locale', $locale);
    }
}

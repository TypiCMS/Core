<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LocaleController extends BaseAdminController
{
    /**
     * Change content locale.
     *
     * @param mixed $locale
     */
    public function setContentLocale($locale)
    {
        Session::put('allLocalesInForm', false);
        if ($locale === 'all') {
            Session::put('allLocalesInForm', true);
        } else {
            Session::put('content_locale', $locale);
        }
    }
}

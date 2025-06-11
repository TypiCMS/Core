<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LocaleController extends BaseAdminController
{
    /**
     * Change content locale.
     */
    public function setContentLocale(string $locale): void
    {
        Session::put('allLocalesInForm', false);
        if ($locale === 'all') {
            Session::put('allLocalesInForm', true);
        } else {
            Session::put('content_locale', $locale);
        }
    }
}

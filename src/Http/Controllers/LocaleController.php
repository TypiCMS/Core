<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Session;

final class LocaleController extends BaseAdminController
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

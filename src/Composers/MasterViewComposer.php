<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class MasterViewComposer
{
    public function compose(View $view)
    {
        $view->with('websiteTitle', TypiCMS::title());
        $navbar = false;
        $user = auth('web')->user();
        if ($user && $user->can('see navbar') && !request('preview')) {
            $navbar = true;
        }
        $view->with('navbar', $navbar);
    }
}

<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class MasterViewComposer
{
    public function compose(View $view)
    {
        $view->with('websiteTitle', app('typicms')->title());
        $navbar = false;
        $user = auth()->user();
        if (
            $user &&
            (
                $user->can('see-navbar') ||
                $user->isSuperUser()
            ) &&
            !request('preview')
        ) {
            $navbar = true;
        }
        $view->with('navbar', $navbar);
    }
}

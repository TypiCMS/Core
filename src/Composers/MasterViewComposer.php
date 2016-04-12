<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class MasterViewComposer
{
    public function compose(View $view)
    {
        $view->with('websiteTitle', app('typicms')->title());
        if (
            auth()->user() &&
            (
                auth()->user()->hasAnyRole(['administrator', 'editor']) ||
                auth()->user()->isSuperUser()
            ) &&
            !request()->input('preview'))
        {
            $view->with('navbar', true);
        }
    }
}

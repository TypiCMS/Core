<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Contracts\View\View;

class MasterViewComposer
{
    public function compose(View $view): void
    {
        $view->with('websiteTitle', websiteTitle());
        $navbar = false;
        $user = auth('web')->user();
        if ($user && $user->can('see navbar') && !request()->boolean('preview')) {
            $navbar = true;
        }
        $view->with('navbar', $navbar);
    }
}

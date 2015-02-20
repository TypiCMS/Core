<?php namespace TypiCMS\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class MasterViewComposer
{
    public function compose(View $view)
    {
        $view->with('websiteTitle', config('typicms.' . App::getLocale() . '.website_title'));
    }
}

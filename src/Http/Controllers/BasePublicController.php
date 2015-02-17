<?php
namespace TypiCMS\Http\Controllers;

use App;
use Config;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Patchwork\Utf8;
use Sentry;
use View;

abstract class BasePublicController extends BaseController
{

    protected $repository;

    // The cool kids’ way of handling page titles.
    // https://gist.github.com/jonathanmarvens/6017139
    public $applicationName;
    public $title  = array(
        'parent'    => '',
        'separator' => '',
        'child'     => '',
    );

    public function __construct($repository = null)
    {
        $this->repository = $repository;

        $this->applicationName = Config::get('typicms.' . App::getLocale() . '.website_title');

        $instance = $this;
        // View::composer($this->layout, function (\Illuminate\View\View $view) use ($instance) {
        //     $view->withTitle(Utf8::ucfirst(implode(' ', $instance->title)) . ' – ' . $instance->applicationName);
        // });

        $bodyClass = ['lang-' . App::getLocale(), $repository->getModel()->getTable()];
        if (Sentry::getUser() && ! Input::get('preview')) {
            $bodyClass[] = 'has-navbar';
        }
        View::share('lang', App::getLocale());
        View::share('bodyClass', implode(' ', $bodyClass));
    }
}

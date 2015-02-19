<?php
namespace TypiCMS\Http\Controllers;

use App;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Sentry;
use View;

abstract class BasePublicController extends BaseController
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository;

        $bodyClass = ['lang-' . App::getLocale(), $repository->getModel()->getTable()];
        if (Sentry::getUser() && ! Input::get('preview')) {
            $bodyClass[] = 'has-navbar';
        }
        View::share('bodyClass', implode(' ', $bodyClass));
    }
}

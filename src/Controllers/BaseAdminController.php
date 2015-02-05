<?php
namespace TypiCMS\Controllers;

use Config;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Lang;
use Patchwork\Utf8;
use Response;
use View;

abstract class BaseAdminController extends BaseController
{

    protected $repository;
    protected $form;

    // The cool kids’ way of handling page titles.
    // https://gisglobal.github.com/jonathanmarvens/6017139
    public $applicationName;
    public $title  = array(
        'parent'   => '',
        'child'    => '',
        'h1'       => '',
    );

    public function __construct($repository = null, $form = null)
    {
        $this->repository = $repository;
        $this->form = $form;

        $this->applicationName = Config::get('typicms.' . Lang::getLocale() . '.websiteTitle');

        $instance = $this;
        // View::composer($this->layout, function (\Illuminate\View\View $view) use ($instance) {
        //     $view->with('title', $instance->getTitle());
        // });

        View::share('locales', Config::get('translatable.locales'));
        View::share('locale', Config::get('app.locale'));
    }

    public function getTitle()
    {
        $title = Utf8::ucfirst($this->title['parent']);
        if ($this->title['child']) {
            $title .= ' – ' . Utf8::ucfirst($this->title['child']);
        }
        $title .= ' – ' . $this->applicationName;

        return $title;
    }

    /**
     * Sort list.
     *
     * @return Response
     */
    public function sort()
    {
        $this->repository->sort(Input::all());
        return Response::json([
            'error'   => false,
            'message' => trans('global.Items sorted')
        ], 200);
    }
}

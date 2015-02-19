<?php
namespace TypiCMS\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Input;
use Response;

abstract class BaseAdminController extends BaseController
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository;
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

    /**
     * Redirect after a form is saved
     * 
     * @param  $request
     * @param  $model
     * @return \Illuminate\Routing\Redirector
     */
    protected function redirect($request, $model)
    {
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl() ;
        return redirect($redirectUrl);
    }
}

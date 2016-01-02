<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;

abstract class BaseAdminController extends Controller
{
    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('admin');
        $this->repository = $repository;
    }

    /**
     * List models.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $module = $this->repository->getTable();
        $title = trans($module.'::global.name');

        return view('core::admin.index')
            ->with(compact('title', 'module'));
    }

    /**
     * Sort list.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sort()
    {
        $this->repository->sort(Request::all());

        return response()->json([
            'error'   => false,
            'message' => trans('global.Items sorted'),
        ], 200);
    }

    /**
     * Redirect after a form is saved.
     *
     * @param  $request
     * @param  $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect($request, $model)
    {
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl();

        return redirect($redirectUrl);
    }
}

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create($parent = null)
    {
        $module = $this->repository->getTable();
        $model = $this->repository->getModel();

        return view('core::admin.create')
            ->with(compact('model', 'module'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $model
     *
     * @return \Illuminate\View\View
     */
    public function edit($model, $child = null)
    {
        return view('core::admin.edit')
            ->with(compact('model'));
    }

    /**
     * Show resource.
     *
     * @param  $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($model, $child = null)
    {
        return redirect($model->editUrl());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $model
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy($model, $child = null)
    {
        if ($this->repository->delete($model)) {
            return back();
        }
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

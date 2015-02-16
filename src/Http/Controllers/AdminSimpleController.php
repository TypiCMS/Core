<?php
namespace TypiCMS\Http\Controllers;

use Input;
use Redirect;
use View;

abstract class AdminSimpleController extends BaseAdminController
{

    /**
     * List models
     * 
     * @return View
     */
    public function index()
    {
        return view('core::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $model = $this->repository->getModel();
        return view('core::admin.create')
            ->with(compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $model
     * @return void
     */
    public function edit($model)
    {
        return view('core::admin.edit')
            ->with(compact('model'));
    }

    /**
     * Show resource.
     *
     * @param  $model
     * @return Redirect
     */
    public function show($model)
    {
        return Redirect::to($model->editUrl());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $model
     * @return Redirect
     */
    public function destroy($model)
    {
        if ($this->repository->delete($model)) {
            return Redirect::back();
        }
    }
}

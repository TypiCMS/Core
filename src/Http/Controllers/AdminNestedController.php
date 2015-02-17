<?php
namespace TypiCMS\Http\Controllers;

use Input;
use Redirect;
use View;

abstract class AdminNestedController extends BaseAdminController
{

    /**
     * List models
     * 
     * @param  Model $parent
     * @return void
     */
    public function index($parent = null)
    {
        return view('core::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  $parent
     * @return void
     */
    public function create($parent = null)
    {
        $model = $this->repository->getModel();
        return view('core::admin.create')
            ->with(compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $parent
     * @param  $model
     * @return void
     */
    public function edit($parent = null, $model)
    {
        return view('core::admin.edit')
            ->with(compact('model'));
    }

    /**
     * Show resource.
     *
     * @param  $parent
     * @param  $model
     * @return Redirect
     */
    public function show($parent = null, $model)
    {
        return Redirect::to($model->editUrl());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $parent
     * @param  $model
     * @return Redirect
     */
    public function destroy($parent = null, $model)
    {
        if ($this->repository->delete($model)) {
            return Redirect::back();
        }
    }
}

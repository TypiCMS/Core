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
     * Store a newly created resource in storage.
     *
     * @return Redirect
     */
    public function store()
    {

        if ($model = $this->form->save(Input::all())) {
            $redirectUrl = Input::get('exit') ? $model->indexUrl() : $model->editUrl() ;
            return Redirect::to($redirectUrl);
        }

        return Redirect::route('admin.' . $this->repository->getTable() . '.create')
            ->withInput()
            ->withErrors($this->form->errors());

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $model
     * @return Redirect
     */
    public function update($model)
    {

        if ($this->form->update(Input::all())) {
            $redirectUrl = Input::get('exit') ? $model->indexUrl() : $model->editUrl() ;
            return Redirect::to($redirectUrl);
        }

        return Redirect::to($model->editUrl())
            ->withInput()
            ->withErrors($this->form->errors());

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

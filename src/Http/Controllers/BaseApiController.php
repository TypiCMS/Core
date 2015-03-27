<?php
namespace TypiCMS\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Input;
use Response;

abstract class BaseApiController extends Controller
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('TypiCMS\Http\Middleware\Admin');
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->repository = $repository;
    }

    /**
     * Get models
     * 
     * @return Response
     */
    public function index()
    {
        $models = $this->repository->all([], true);
        return Response::json($models, 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  $model
     * @return Response
     */
    public function show($model)
    {
        return Response::json($model, 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  $model
     * @return Response
     */
    public function edit($model)
    {
        return Response::json($model, 200);
    }

    /**
     * Store a new resource in storage.
     * 
     * @return Response
     */
    public function store()
    {
        $model = $this->repository->create(Input::all());
        return Response::json([
            'error'   => false,
            'message' => 'Item saved',
            'model'   => $model
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  $model
     * @return Response
     */
    public function update($model)
    {
        $this->repository->update(Input::all());
        return Response::json([
            'error'   => false,
            'message' => 'Item updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  $model
     * @return Response
     */
    public function destroy($model)
    {
        $this->repository->delete($model);
        return Response::json([
            'error'   => false,
            'message' => 'Item deleted'
        ], 200);
    }
}

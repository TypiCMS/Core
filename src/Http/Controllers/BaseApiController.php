<?php
namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;

abstract class BaseApiController extends Controller
{

    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('admin');
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->repository = $repository;
    }

    /**
     * Get models
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $models = $this->repository->all([], true);
        return response()->json($models, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $model
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($model)
    {
        return response()->json($model, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $model
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($model)
    {
        return response()->json($model, 200);
    }

    /**
     * Store a new resource in storage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store()
    {
        $model = $this->repository->create(Input::all());
        return response()->json([
            'error'   => false,
            'message' => 'Item saved',
            'model'   => $model
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $model
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($model)
    {
        $this->repository->update(Input::all());
        return response()->json([
            'error'   => false,
            'message' => 'Item updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $model
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($model)
    {
        $this->repository->delete($model);
        return response()->json([
            'error'   => false,
            'message' => 'Item deleted'
        ], 200);
    }
}

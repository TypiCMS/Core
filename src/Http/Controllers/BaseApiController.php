<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;

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
     * List resources.
     *
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($model)
    {
        return response()->json($model, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($model)
    {
        return response()->json($model, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $model
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($model)
    {
        $this->repository->delete($model);

        return response()->json([
            'error'   => false,
            'message' => 'Item deleted',
        ], 200);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;

abstract class BaseApiController extends Controller
{
    /**
     *  Array of endpoints that do not require authorization.
     */
    protected $publicEndpoints = [];

    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('api', ['except' => $this->publicEndpoints]);
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
}

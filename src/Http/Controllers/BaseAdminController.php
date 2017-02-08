<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class BaseAdminController extends Controller
{
    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('admin');
        $this->repository = $repository;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param integer $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ajaxUpdate($id, Request $request)
    {
        $updated = $this->repository->update($id, $request->all());

        return response()->json([
            'error' => !$updated,
        ]);
    }

    /**
     * Sort list.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sort()
    {
        $this->repository->sort(request()->all());

        return response()->json([
            'error' => false,
            'message' => __('Items sorted'),
        ]);
    }

    /**
     * Sync related items for model.
     *
     * @param Model  $model
     * @param array  $data
     * @param string $table
     */
    public function syncRelation($model, array $data, $table = null)
    {
        if (!method_exists($model, $table)) {
            return false;
        }

        if (!isset($data[$table])) {
            return false;
        }

        // add related items
        $pivotData = [];
        $position = 0;
        if (is_array($data[$table])) {
            foreach ($data[$table] as $id) {
                $pivotData[$id] = ['position' => $position++];
            }
        }

        // Sync related items
        $model->$table()->sync($pivotData);
    }

    /**
     * Redirect after a form is saved.
     *
     * @param $request
     * @param $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect($request, $model)
    {
        if (is_array($model)) {
            $model = end($model);
        }
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl();

        return redirect($redirectUrl);
    }
}

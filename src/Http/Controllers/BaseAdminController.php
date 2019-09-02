<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

abstract class BaseAdminController extends Controller
{
    protected $repository;

    public function __construct($repository = null)
    {
        $this->middleware('verified:'.config('typicms.admin_locale').'::verification.notice');
        $this->repository = $repository;
    }

    protected function redirect($request, $model): RedirectResponse
    {
        if (is_array($model)) {
            $model = end($model);
        }
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl();

        return redirect($redirectUrl);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class BaseAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified:' . app()->getLocale() . '::verification.notice');
    }

    protected function redirect(Request $request, mixed $model): RedirectResponse
    {
        if (is_array($model)) {
            $model = end($model);
        }
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl();

        return redirect($redirectUrl);
    }
}

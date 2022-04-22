<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

abstract class BaseAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified:'.config('app.locale').'::verification.notice');
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

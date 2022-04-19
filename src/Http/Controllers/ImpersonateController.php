<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Models\User;

class ImpersonateController extends Controller
{
    public function start($id)
    {
        $user = User::find($id);

        // Guard against administrator impersonate
        if (auth()->user()->can('impersonate users') && !$user->isSuperUser()) {
            Session::put('impersonation', $user->id);
        } else {
            return back()->withError(__('A Superuser can not be impersonated.'));
        }

        return redirect(TypiCMS::homeUrl());
    }

    public function stopImpersonation()
    {
        Session::forget('impersonation');

        return redirect()->route('admin::dashboard');
    }
}

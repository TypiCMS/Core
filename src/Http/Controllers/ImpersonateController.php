<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use TypiCMS\Modules\Core\Models\User;

final class ImpersonateController extends Controller
{
    public function start(int $id): RedirectResponse
    {
        $user = User::query()->find($id);

        // Guard against administrator impersonate
        if (auth()->user()->can('impersonate users') && !$user->isSuperUser()) {
            Session::put('impersonation', $user->id);
        } else {
            return back()->withError(__('A Superuser can not be impersonated.'));
        }

        return redirect(homeUrl());
    }

    public function stopImpersonation(): RedirectResponse
    {
        Session::forget('impersonation');

        return to_route('admin::dashboard');
    }
}

<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\ProfileFormRequest;

final class ProfileController extends BaseAdminController
{
    public function edit(): View
    {
        $user = Auth::user();
        $passkeys = $user->passkeys()->get(['id', 'name', 'last_used_at']);

        return view('users::admin.profile', [
            'model' => $user,
            'passkeys' => $passkeys,
        ]);
    }

    public function update(ProfileFormRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        return redirect()->route('admin::profile')->with('success', __('Profile updated.'));
    }
}

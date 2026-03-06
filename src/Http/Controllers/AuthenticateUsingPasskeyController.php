<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController as BaseController;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;

final class AuthenticateUsingPasskeyController extends BaseController
{
    public function __invoke(AuthenticateUsingPasskeysRequest $request): RedirectResponse
    {
        $result = parent::__invoke($request);

        $user = auth()->user();

        if ($user && !$user->activated) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session()->flash('authenticatePasskey::message', __('Your account is not activated.'));

            return back();
        }

        return $result;
    }
}

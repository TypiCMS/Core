<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm(): View
    {
        return view('users::login');
    }

    protected function sendFailedLoginResponse(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $this->credentials($request);

        $user = User::query()->where('email', $credentials['email'])->first();
        if (!$user) {
            $error = __('This user was not found.');
        } elseif (!$user->activated) {
            $error = __('This user is not activated.');
        } else {
            $error = __('The password is incorrect.');
        }

        $errors = [$this->username() => $error];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /** @return array<string, string|int> */
    protected function credentials(Request $request): array
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['activated'] = 1;

        return $credentials;
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\OneTimePasswords\Rules\OneTimePasswordRule;

class AuthController extends Controller
{
    protected string $redirectTo = '/';

    protected ?string $email = null;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'showPasskeyCreationForm']);
    }

    public function showPasskeyLoginForm(): View
    {
        return view('users::login');
    }

    public function showOneTimePasswordLoginForm(): View
    {
        return view('users::login-otp');
    }

    public function showOneTimePasswordForm(): View
    {
        return view('users::login-code');
    }

    public function showPasskeyCreationForm(): View|RedirectResponse
    {
        if (auth()->check() && auth()->user()->passkeys->isNotEmpty()) {
            return redirect()->route('admin::dashboard');
        }

        return view('users::create-passkey');
    }

    protected function submitOneTimePasswordLoginForm(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|exists:users,email',
        ])->validate();

        $this->email = $request->string('email');
        $user = $this->findUser();

        if ($this->rateLimitHit()) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['$errors' => __('Please try later.')]);
        }

        session(['email' => $this->email]);

        $user->sendOneTimePassword();

        return redirect()->route(app()->getLocale() . '::login-code')
            ->withInput($request->only('email'))
            ->with('status', __('A one-time password has been sent to your email address.'));
    }

    public function submitOneTimePassword(Request $request): RedirectResponse
    {
        if (session()->missing('email')) {
            return redirect()
                ->route(app()->getLocale() . '::otp-login')
                ->with('status', __('Please enter your email address first.'));
        }
        $this->email = session('email');
        session()->forget('email');
        $user = $this->findUser();

        Validator::make($request->all(), [
            'one_time_password' => ['required', new OneTimePasswordRule($user)],
        ])->validate();

        auth()->login($user);

        if ($user->passkeys->isEmpty()) {
            return redirect()->route(app()->getLocale() . '::create-passkey');
        }

        return redirect()->route('admin::dashboard');
    }

    protected function findUser(): ?Authenticatable
    {
        $authenticatableModel = config('auth.providers.users.model');

        return $authenticatableModel::query()->firstWhere('email', $this->email);
    }

    protected function rateLimitHit(): bool
    {
        $rateLimitKey = "one-time-password-component-send-code.{$this->email}";

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            return true;
        }

        RateLimiter::hit($rateLimitKey, 60); // 60 seconds decay time

        return false;
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    protected function guard(): StatefulGuard|Guard
    {
        return Auth::guard();
    }
}

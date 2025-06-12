<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\UserRegistrationFormRequest;
use TypiCMS\Modules\Core\Models\User;

class RegisterController extends Controller
{
    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(): View
    {
        return view('users::register');
    }

    public function register(UserRegistrationFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (User::query()->where('email', $data['email'])->exists()) {
            return redirect()
                ->route(app()->getLocale() . '::login')
                ->withStatus(__('An account already exists for this email address.'));
        }

        $user = User::query()->create($data);

        event(new Registered($user));

        return redirect()
            ->back()
            ->with('status', __('Your account has been created, check your email for the one time login code.'));
    }
}

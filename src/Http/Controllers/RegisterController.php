<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\UserRegistrationFormRequest;
use TypiCMS\Modules\Core\Models\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('users::register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(UserRegistrationFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists()) {
            return redirect()
                ->route(app()->getLocale().'::login')
                ->withStatus(__('An account already exists for this email address. Log in or request a new password.'));
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        event(new Registered($user));

        return redirect()
            ->back()
            ->with('status', __('Your account has been created, check your email for the verification link.'));
    }
}

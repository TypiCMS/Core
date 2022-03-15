<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected function redirectTo()
    {
        return route(app()->getLocale().'::login');
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param null|mixed $token
     */
    public function showResetForm(Request $request, $token = null): View
    {
        return view('users::passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param mixed $user
     * @param mixed $password
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }
}

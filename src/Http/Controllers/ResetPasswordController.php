<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected function redirectTo(): string
    {
        return route(app()->getLocale() . '::login');
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('users::passwords.reset')->with(
            ['token' => $token, 'email' => $request->string('email')]
        );
    }

    protected function resetPassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
    }
}

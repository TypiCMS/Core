<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm(): View
    {
        return view('users::login');
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $credentials = $this->credentials($request);

        $user = User::where('email', $credentials['email'])->first();
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

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request): array
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['activated'] = 1;

        return $credentials;
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use TypiCMS\Modules\Core\Models\User;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     */
    public function redirectTo()
    {
        return route(app()->getLocale().'::verification.verified');
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except('verify', 'verified');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('users::verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        if ($user = User::find($request->route('id'))) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    public function verified()
    {
        if (session('verified')) {
            return view('users::verified');
        }

        return redirect('/');
    }
}

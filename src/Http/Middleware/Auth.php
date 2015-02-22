<?php
namespace TypiCMS\Http\Middleware;

use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Closure;
use Config;
use Log;
use Redirect;
use Request;
use Response;
use Sentry;

class Auth
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Sentry::check()) {
            if (Request::ajax()) {
                return Response::make('Unauthorized', 401);
            }
            return Redirect::guest(route('login'));
        }

        $value = str_replace(['api.', 'admin.'], '', $request->route()->getName());

        try {
            $user = Sentry::getUser();
            if (! $user->hasAccess($value)) {
                abort(403);
            }
        } catch (UserNotFoundException $e) {
            Log::error($e->getMessage() . '\n' .  $request->fullUrl());
            return Redirect::guest(route('login'));
        }

        return $next($request);
    }

}

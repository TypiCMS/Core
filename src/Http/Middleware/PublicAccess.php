<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

class PublicAccess
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            !in_array($request->ip(), config('typicms.authorized_ips', []))
            && config('typicms.auth_public')
            && !Auth::check()
            && !Route::is('*::login*')
            && !Route::is('*::password*')
        ) {
            if ($request->ajax()) {
                return Response::make('Unauthorized', 401);
            }

            return Redirect::guest(route(app()->getLocale().'::login'));
        }

        return $next($request);
    }
}

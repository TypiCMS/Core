<?php
namespace TypiCMS\Http\Middleware;

use Closure;
use Sentry;
use Request;
use Response;
use Redirect;

class PublicAccess
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
        if (config('typicms.auth_public') && ! Sentry::check()) {
            if (Request::ajax()) {
                return Response::make('Unauthorized', 401);
            }
            return Redirect::guest(route('login'));
        }
        return $next($request);
    }

}

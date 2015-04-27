<?php
namespace TypiCMS\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Redirect;
use Request;
use Response;
use Sentry;

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

        $response = $next($request);

        if (
            $response instanceof View &&
            $request->method() == 'GET' &&
            ! Sentry::check() &&
            config('typicms.html_cache') &&
            ! config('app.debug')
        ) {
            $directory = public_path() . '/cache/html' . $request->getRequestUri();
            if ( ! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            File::put($directory.'/index.html', $response->render());
        }

        return $response;
    }

}

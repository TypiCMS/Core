<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

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
        if (config('typicms.auth_public') && !Auth::check()) {
            if (Request::ajax()) {
                return Response::make('Unauthorized', 401);
            }
            return Redirect::guest(route('login'));
        }

        $response = $next($request);

        // HTML cache
        if (
            $response instanceof View &&
            $request->method() == 'GET' &&
            !Auth::check() &&
            $this->queryStringIsEmptyOrOnlyPage($request) &&
            !config('app.debug') &&
            config('typicms.html_cache')
        ) {
            $directory = public_path() . '/html' . $request->getPathInfo();
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            File::put($directory . '/index' . $request->getQueryString() . '.html', $response->render());
        }

        return $response;
    }

    /**
     * Return false if there is query param other than page=
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    private function queryStringIsEmptyOrOnlyPage($request)
    {
        $nbInputs = count($request->input());
        if ($nbInputs == 0) {
            return true;
        }
        if ($request->input('page') && $nbInputs == 1) {
            return true;
        }
        return false;
    }

}

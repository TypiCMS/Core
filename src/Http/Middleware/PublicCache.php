<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Pages;

class PublicCache
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
        $response = $next($request);

        /**
         * If page is not cacheable, don’t generate static html.
         */
        if (isset($response->original->page) && $response->original->page->no_cache) {
            return $response;
        }

        // HTML cache
        if (
            !$response->isRedirection() &&
            $request->isMethod('get') &&
            !Auth::check() &&
            $this->queryStringIsEmptyOrOnlyPage($request) &&
            !config('app.debug') &&
            config('typicms.html_cache')
        ) {
            $directory = public_path() . '/html' . $request->getPathInfo();
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            File::put($directory . '/index' . $request->getQueryString() . '.html', $response->content());
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

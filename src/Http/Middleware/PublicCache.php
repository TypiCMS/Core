<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PublicCache
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // HTML cache
        if (
            !$response->isRedirection() &&
            $request->isMethod('get') &&
            !Auth::check() &&
            $this->queryStringIsEmptyOrOnlyPage($request) &&
            !config('app.debug') &&
            config('typicms.html_cache')
        ) {
            if ($this->hasPageThatShouldNotBeCached($response)) {
                return $response;
            }
            $directory = public_path().'/html'.$request->getPathInfo();
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            File::put($directory.'/index'.$request->getQueryString().'.html', $response->content());
        }

        return $response;
    }

    /**
     * Does the response has a page?
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return bool
     */
    private function hasPageThatShouldNotBeCached(Response $response)
    {
        return !$response->original->page || isset($response->original->page) && $response->original->page->no_cache;
    }

    /**
     * Return false if there is query param other than page=.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function queryStringIsEmptyOrOnlyPage(Request $request)
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

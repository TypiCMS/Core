<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
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

        // Get page from uri
        $patterns = [];
        foreach (config('translatable.locales') as $locale) {
            $patterns[] = '#^' . $locale . '/#';
            $patterns[] = '#^' . $locale . '$#';
        }
        $uri = trim(preg_replace($patterns, '', $request->path()), '/');
        if ($uri === '') {
            $uri = null;
        }
        $page = Pages::getFirstByUri($uri, config('app.locale'), ['translations']);

        // HTML cache
        if (
            // $response instanceof View &&
            $page->cacheable() &&
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

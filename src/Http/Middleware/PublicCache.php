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

        if ($this->uncacheablePage($request->path())) {
            return $response;
        }

        // HTML cache
        if (
            // $response instanceof View &&
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

    /**
     * Check if request is pointing to a page and it's cacheable
     *
     * @param  string $path
     * @return boolean
     */
    private function uncacheablePage($path)
    {
        // Get page from uri
        $patterns = [];
        foreach (config('translatable.locales') as $locale) {
            $patterns[] = '#^' . $locale . '/#';
            $patterns[] = '#^' . $locale . '$#';
        }
        $uri = trim(preg_replace($patterns, '', $path), '/');
        if ($uri === '') {
            $uri = null;
        }
        try {
            $page = Pages::getFirstByUri($uri, config('app.locale'), ['translations']);
            return (bool) $page->no_cache;
        } catch (Exception $e) {
            return false;
        }
    }
}

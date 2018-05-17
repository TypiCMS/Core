<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JavaScript;

class JavaScriptData
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
        $locales = [];
        foreach (locales() as $locale) {
            $locales[] = $locale;
        }
        JavaScript::put([
            'locales' => $locales,
            'content_locale' => config('typicms.content_locale'),
            'locale' => config('app.locale'),
        ]);

        return $next($request);
    }
}

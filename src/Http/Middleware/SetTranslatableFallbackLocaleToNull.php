<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;

class SetTranslatableFallbackLocaleToNull
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config(['app.fallback_locale' => null]);
        config(['translatable.fallback_locale' => null]);

        return $next($request);
    }
}

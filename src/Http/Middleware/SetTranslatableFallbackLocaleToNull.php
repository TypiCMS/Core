<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTranslatableFallbackLocaleToNull
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        config(['app.fallback_locale' => null]);

        return $next($request);
    }
}

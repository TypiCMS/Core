<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;

class Registration
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('typicms.register')) {
            abort(404);
        }

        return $next($request);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authorization
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
        $permission = str_replace(['api::', 'admin::'], '', $request->route()->getName());
        if ($request->user()->cannot($permission)) {
            abort(403);
        }

        return $next($request);
    }
}

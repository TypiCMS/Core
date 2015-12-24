<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
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
    public function handle($request, Closure $next)
    {
        $value = str_replace(['api.', 'admin.'], '', $request->route()->getName());
        $user = Auth::user();
        if (!$user->hasAccess($value)) {
            abort(403);
        }
        return $next($request);
    }
}

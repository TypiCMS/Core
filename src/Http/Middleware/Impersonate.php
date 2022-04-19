<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('impersonation')) {
            Auth::onceUsingId($request->session()->get('impersonation'));
        }

        return $next($request);
    }
}

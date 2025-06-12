<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->session()->has('impersonation')) {
            Auth::onceUsingId($request->session()->get('impersonation'));
        }

        return $next($request);
    }
}

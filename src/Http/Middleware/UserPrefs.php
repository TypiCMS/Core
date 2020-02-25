<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserPrefs
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // set curent user preferences to Config
        if ($user = $request->user()) {
            Config::set('typicms.user', $user->preferences);
        }

        return $next($request);
    }
}

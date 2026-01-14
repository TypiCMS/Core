<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PoweredByHeader
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('typicms.send_powered_by_header') && $response instanceof Response) {
            $response->header('X-Powered-By', 'TypiCMS');
        }

        return $response;
    }
}

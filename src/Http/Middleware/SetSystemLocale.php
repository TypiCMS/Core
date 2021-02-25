<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetSystemLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = config('typicms.locales')[app()->getLocale()] ?? null;
        if ($locale === null) {
            // For backward compatibility.
            $locale = app()->getLocale().'_'.mb_strtoupper(app()->getLocale());
        }
        setlocale(LC_ALL, $locale.'.utf8', $locale.'.utf-8', $locale.'.UTF-8', $locale);

        return $next($request);
    }
}

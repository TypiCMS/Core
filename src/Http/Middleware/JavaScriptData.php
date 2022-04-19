<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class JavaScriptData
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $data = [
            'content_locale' => config('typicms.content_locale'),
            'locale' => app()->getLocale(),
            'locale_region' => TypiCMS::localeAndRegion('-'),
        ];
        foreach (locales() as $locale) {
            $data['locales'][] = [
                'short' => $locale,
                'long' => trans('languages.'.$locale),
            ];
        }
        if (auth()->check()) {
            $data['permissions'] = $request->user()->allPermissions;
        }
        app('JavaScript')->put($data);

        return $next($request);
    }
}

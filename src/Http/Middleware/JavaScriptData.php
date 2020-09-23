<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
            'locale' => config('app.locale'),
        ];
        foreach (locales() as $locale) {
            $data['locales'][] = [
                'short' => $locale,
                'long' => trans('languages.'.$locale),
            ];
        }

        app('JavaScript')->put($data);

        return $next($request);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;

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
            'locale_region' => localeAndRegion('-'),
            'max_file_upload_size' => config('typicms.max_file_upload_size'),
        ];
        foreach (locales() as $locale) {
            $data['locales'][] = [
                'short' => $locale,
                'long' => trans('languages.' . $locale),
            ];
        }
        if (auth()->check()) {
            $data['permissions'] = $request->user()->allPermissions;
        }
        $data['public_css_file'] = Vite::asset('resources/scss/public.scss');

        app('JavaScript')->put($data);

        return $next($request);
    }
}

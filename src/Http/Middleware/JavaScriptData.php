<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use JavaScript;

class JavaScriptData
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
        $locales = [];
        foreach (config('translatable-bootforms.locales') as $locale) {
            $locales[$locale] = [
                'short' => $locale,
                'long'  => trans('global.languages.'.$locale),
            ];
        }
        $locales['all'] = [
            'short' => 'all',
            'long'  => trans('global.languages.all'),
        ];
        JavaScript::put([
            '_token'          => csrf_token(),
            'encrypted_token' => Crypt::encrypt(csrf_token()),
            'locales'         => $locales,
        ]);

        return $next($request);
    }
}

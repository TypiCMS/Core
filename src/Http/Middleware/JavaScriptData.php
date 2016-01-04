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
        foreach (config('translatable.locales') as $locale) {
            $locales[] = [
                'short' => $locale,
                'long'  => trans('global.languages.'.$locale),
            ];
        }
        JavaScript::put([
            '_token'          => csrf_token(),
            'encrypted_token' => Crypt::encrypt(csrf_token()),
            'locales'         => $locales,
        ]);

        return $next($request);
    }
}

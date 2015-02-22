<?php
namespace TypiCMS\Http\Middleware;

use App;
use Closure;
use Config;
use Input;
use Redirect;
use Request;
use Sentry;

class PublicLocale
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $firstSegment = Request::segment(1);
        $locale = App::getLocale();

        if (in_array($firstSegment, config('translatable.locales'))) {
            $locale = $firstSegment;
        }

        // Throw a 404 if website in this language is offline
        if (! Config::get('typicms.' . $locale . '.status')) {
            abort(404);
        }

        // Remove preview param if no admin user connected
        if (Input::get('preview') && ! Sentry::check()) {
            return Redirect::to(Request::path());
        }

        return $next($request);
    }
}

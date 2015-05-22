<?php
namespace TypiCMS\Modules\Core\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Log;

class Authenticate
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('login'));
            }
        }

        $value = str_replace(['api.', 'admin.'], '', $request->route()->getName());

        try {
            $user = $this->auth->user();
            if (! $user->hasAccess($value)) {
                abort(403);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage() . '\n' .  $request->fullUrl());
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }

}

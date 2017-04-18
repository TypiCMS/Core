<?php

namespace TypiCMS\Modules\Core\Http\Middleware;

use Exception;
use Closure;
use Illuminate\Http\Request;

class Authorization
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
        $routeName = str_replace('admin::', '', $request->route()->getName());
        try {
            list($action, $module) = explode('-', $routeName);
            if (in_array($action, ['edit', 'sort', 'update'])) {
                $action = 'edit';
                $module = str_singular($module);
            }
            if (in_array($action, ['create', 'store'])) {
                $action = 'create';
                $module = str_singular($module);
            }
            $permission = $action.'-'.$module;
        } catch (Exception $e) {
            $permission = $routeName;
        }
        if ($request->user()->cannot($permission)) {
            abort(403);
        }

        return $next($request);
    }
}

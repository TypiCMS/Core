<?php
namespace TypiCMS\Providers;

use Config;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        /**
         * Register routes filter classes
         */
        $router->filter('public.checkLocale',   'TypiCMS\Filters\PublicFilter@checkLocale');
        $router->filter('admin.setLocale',      'TypiCMS\Filters\AdminFilter@setLocale');
        $router->filter('visitor.publicAccess', 'TypiCMS\Filters\UsersFilter@publicAccess');
        $router->filter('visitor.mayRegister',  'TypiCMS\Filters\UsersFilter@mayRegister');
        $router->filter('user.auth',            'TypiCMS\Filters\UsersFilter@auth');
        $router->filter('user.hasAccess',       'TypiCMS\Filters\UsersFilter@hasAccess');
        $router->filter('user.inGroup',         'TypiCMS\Filters\UsersFilter@inGroup');

        /**
         * Route filter front office
         */
        foreach (Config::get('translatable.locales') as $locale) {
            $router->when($locale,      'public.checkLocale');
            $router->when($locale.'/*', 'public.checkLocale');
        }

        /**
         * Route filter back office
         */
        $router->when('admin',   'admin.setLocale|user.auth|user.hasAccess');
        $router->when('admin/*', 'admin.setLocale|user.auth|user.hasAccess');
        $router->when('api',     'admin.setLocale');
        $router->when('api/*',   'admin.setLocale');
        $router->when('api',     'user.auth|user.hasAccess', ['post', 'put', 'patch', 'delete']);
        $router->when('api/*',   'user.auth|user.hasAccess', ['post', 'put', 'patch', 'delete']);
    }

    public function map()
    {
    }

}

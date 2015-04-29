<?php
namespace TypiCMS\Providers;

use App;
use Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Request;
use TypiCMS\Commands\CacheKeyPrefix;
use TypiCMS\Commands\Database;
use TypiCMS\Commands\Install;
use TypiCMS\Modules\Users\Repositories\SentryUser;
use TypiCMS\Services\TypiCMS;
use TypiCMS\Services\Upload\FileUpload;
use View;

class CoreServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Some route middleware.
     *
     * @var array
     */
    protected $middleware = [
        'admin'        => 'TypiCMS\Http\Middleware\Admin',
        'auth'         => 'TypiCMS\Http\Middleware\Auth',
        'publicAccess' => 'TypiCMS\Http\Middleware\PublicAccess',
        'publicLocale' => 'TypiCMS\Http\Middleware\PublicLocale',
        'registration' => 'TypiCMS\Http\Middleware\Registration',
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'core');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/core'),
            __DIR__ . '/../resources/views/errors' => base_path('resources/views/errors'),
        ], 'views');

        // translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'core');

        /*
        |--------------------------------------------------------------------------
        | New Relic app name
        |--------------------------------------------------------------------------
        */
        if (extension_loaded('newrelic')) {
            newrelic_set_appname('');
        }

        /*
        |--------------------------------------------------------------------------
        | Commands.
        |--------------------------------------------------------------------------|
        */
        $this->commands('command.install');
        $this->commands('command.cachekeyprefix');
        $this->commands('command.database');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $app = $this->app;

        $this->registerMiddleware($app['router']);

        /*
        |--------------------------------------------------------------------------
        | Set app locale in front office.
        |--------------------------------------------------------------------------
        */
        if (Request::segment(1) != 'admin') {

            $firstSegment = Request::segment(1);
            if (in_array($firstSegment, config('translatable.locales'))) {
                Config::set('app.locale', $firstSegment);
            }
            // Not very reliable, need to be refactored
            setlocale(LC_ALL, App::getLocale() . '_' . strtoupper(App::getLocale()) . '.utf8');

        }

        /*
        |--------------------------------------------------------------------------
        | Init list of modules.
        |--------------------------------------------------------------------------
        */
        Config::set('typicms.modules', []);

        /*
        |--------------------------------------------------------------------------
        | TypiCMS utilities.
        |--------------------------------------------------------------------------
        */
        $app['typicms'] = $this->app->share(function ($app) {
            return new TypiCMS;
        });

        /*
        |--------------------------------------------------------------------------
        | TypiCMS upload service.
        |--------------------------------------------------------------------------
        */
        $app['upload.file'] = $this->app->share(function ($app) {
            return new FileUpload;
        });

        /*
        |--------------------------------------------------------------------------
        | Sidebar view creator.
        |--------------------------------------------------------------------------
        */
        $app->view->creator('core::admin._sidebar', 'TypiCMS\Composers\SidebarViewCreator');

        /*
        |--------------------------------------------------------------------------
        | View composers.
        |--------------------------------------------------------------------------|
        */
        $app->view->composers([
            'TypiCMS\Composers\MasterViewComposer' => '*',
            'TypiCMS\Composers\LocaleComposer' => '*::public.*',
            'TypiCMS\Composers\LocalesComposer' => ['*::*._form'],
        ]);

        $this->registerCommands();
        $this->registerModuleRoutes();
        $this->registerCoreModules();

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Register middleware.
     *
     * @param  Router $router
     * @return void
     */
    private function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $name => $class) {
            $router->middleware($name, $class);
        }
    }

    /**
     * Register artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->bind('command.install', function (Application $app) {
            return new Install(
                new SentryUser($app['sentry']),
                new Filesystem
            );
        });
        $this->app->bind('command.cachekeyprefix', function () {
            return new CacheKeyPrefix(new Filesystem);
        });
        $this->app->bind('command.database', function () {
            return new Database(new Filesystem);
        });
    }

    /**
     * Get routes from menu links.
     *
     * @return void
     */
    private function registerModuleRoutes()
    {
        $this->app->singleton('TypiCMS.routes', function (Application $app) {
            return $app->make('TypiCMS\Modules\Pages\Repositories\PageInterface')->getForRoutes();
        });
    }

    /**
     * Register core modules.
     *
     * @return void
     */
    private function registerCoreModules()
    {
        $app = $this->app;
        $app->register('TypiCMS\Modules\Translations\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Blocks\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Settings\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\History\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Users\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Groups\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Files\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Galleries\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Tags\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Dashboard\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Menus\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Sitemap\Providers\ModuleProvider');
        // Pages and menulinks need to be at last for routing to work.
        $app->register('TypiCMS\Modules\Menulinks\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Pages\Providers\ModuleProvider');
    }

}

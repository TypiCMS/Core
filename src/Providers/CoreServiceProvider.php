<?php
namespace TypiCMS\Providers;

use App;
use Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
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
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        // Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'core');
        $this->publishes([
            __DIR__ . '/../views' => base_path('resources/views/vendor/core'),
        ], 'views');
        $this->publishes([
            __DIR__ . '/../views/errors' => base_path('resources/views/errors'),
        ]);

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
        | HTML macros.
        |--------------------------------------------------------------------------|
        */
        require __DIR__ . '/../Macros.php';

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

        /**
         * Register route service provider
         */
        $app->register('TypiCMS\Providers\RouteServiceProvider');

        /*
        |--------------------------------------------------------------------------
        | Set app locale on front office.
        |--------------------------------------------------------------------------
        */
        if (Request::segment(1) != 'admin') {

            $firstSegment = Request::segment(1);
            if (in_array($firstSegment, Config::get('translatable.locales'))) {
                Config::set('app.locale', $firstSegment);
            }
            // Not very reliable, need to be refactored
            setlocale(LC_ALL, App::getLocale() . '_' . ucfirst(App::getLocale()));

        }

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
        $app->view->creator('core::admin._sidebar', 'TypiCMS\Composers\SideBarViewCreator');

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

        /*
        |--------------------------------------------------------------------------
        | Bind commands.
        |--------------------------------------------------------------------------
        */
        $app->bind('command.install', function (Application $app) {
            return new Install(
                new SentryUser($app['sentry']),
                new Filesystem
            );
        });
        $app->bind('command.cachekeyprefix', function () {
            return new CacheKeyPrefix(new Filesystem);
        });
        $app->bind('command.database', function () {
            return new Database(new Filesystem);
        });

        /*
        |--------------------------------------------------------------------------
        | Get custom routes for front office modules.
        |--------------------------------------------------------------------------
        */
        $app->singleton('TypiCMS.routes', function (Application $app) {
            return $app->make('TypiCMS\Modules\Menulinks\Repositories\MenulinkInterface')->getForRoutes();
        });

        /*
        |--------------------------------------------------------------------------
        | Register core modules.
        |--------------------------------------------------------------------------
        */
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}

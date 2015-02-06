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
        // include __DIR__ . '/../start.php';

        // Add dirs
        // View::addLocation(__DIR__ . '/../Views');
        View::addNamespace('core', __DIR__ . '/../views/');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'core');
        $this->publishes([
            __DIR__ . '/../config/' => config_path('typicms/core'),
        ], 'config');

        // Bring in the routes
        require __DIR__ . '/../routes.php';

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
        /*
        |--------------------------------------------------------------------------
        | Set app locale on public side.
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
        $this->app['typicms'] = $this->app->share(function ($app) {
            return new TypiCMS;
        });

        /*
        |--------------------------------------------------------------------------
        | TypiCMS upload service.
        |--------------------------------------------------------------------------
        */
        $this->app['upload.file'] = $this->app->share(function ($app) {
            return new FileUpload;
        });

        /*
        |--------------------------------------------------------------------------
        | Sidebar view creator.
        |--------------------------------------------------------------------------
        */
        $this->app->view->creator('core::admin._sidebar', 'TypiCMS\SideBarViewCreator');

        /*
        |--------------------------------------------------------------------------
        | Bind commands.
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Get custom routes for public side modules.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('TypiCMS.routes', function (Application $app) {
            return $app->make('TypiCMS\Modules\Menulinks\Repositories\MenulinkInterface')->getForRoutes();
        });

        /*
        |--------------------------------------------------------------------------
        | Register core modules.
        |--------------------------------------------------------------------------
        */
        $this->app->register('TypiCMS\Modules\Translations\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Blocks\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Settings\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\History\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Users\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Groups\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Files\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Galleries\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Tags\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Dashboard\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Menus\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Sitemap\Providers\ModuleProvider');
        // Pages and menulinks need to be at last for routing to work.
        $this->app->register('TypiCMS\Modules\Menulinks\Providers\ModuleProvider');
        $this->app->register('TypiCMS\Modules\Pages\Providers\ModuleProvider');
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

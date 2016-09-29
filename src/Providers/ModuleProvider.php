<?php

namespace TypiCMS\Modules\Core\Providers;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TypiCMS\Modules\Core\Commands\CacheKeyPrefix;
use TypiCMS\Modules\Core\Commands\ClearHtml;
use TypiCMS\Modules\Core\Commands\Create;
use TypiCMS\Modules\Core\Commands\Database;
use TypiCMS\Modules\Core\Commands\Install;
use TypiCMS\Modules\Core\Commands\Publish;
use TypiCMS\Modules\Core\Services\TypiCMS;
use TypiCMS\Modules\Core\Services\Upload\FileUpload;

class ModuleProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return null
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'core');

        $this->publishes([
            __DIR__.'/../resources/views'        => base_path('resources/views/vendor/core'),
            __DIR__.'/../resources/views/errors' => base_path('resources/views/errors'),
        ], 'views');

        // translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'core');

        /*
        |--------------------------------------------------------------------------
        | Commands.
        |--------------------------------------------------------------------------
        */
        $this->commands('command.cachekeyprefix');
        $this->commands('command.clearhtml');
        $this->commands('command.create');
        $this->commands('command.database');
        $this->commands('command.install');
        $this->commands('command.publish');
    }

    /**
     * Register the service provider.
     *
     * @return null
     */
    public function register()
    {
        $app = $this->app;

        /*
         * Register route service provider
         */
        $app->register(\TypiCMS\Modules\Core\Providers\RouteServiceProvider::class);

        /*
         * Collection extensions
         */
        $app->register(\TypiCMS\Modules\Core\Providers\CollectionExtensions::class);

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
        $this->app->singleton('typicms', function () {
            return new TypiCMS();
        });

        /*
        |--------------------------------------------------------------------------
        | TypiCMS upload service.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('upload.file', function () {
            return new FileUpload();
        });

        /*
        |--------------------------------------------------------------------------
        | Sidebar view creator.
        |--------------------------------------------------------------------------
        */
        $app->view->creator('core::admin._sidebar', \TypiCMS\Modules\Core\Composers\SidebarViewCreator::class);

        /*
        |--------------------------------------------------------------------------
        | View composers.
        |--------------------------------------------------------------------------
        */
        $app->view->composers([
            \TypiCMS\Modules\Core\Composers\MasterViewComposer::class => '*',
            \TypiCMS\Modules\Core\Composers\LocaleComposer::class     => '*::public.*',
            \TypiCMS\Modules\Core\Composers\LocalesComposer::class    => '*::admin.*',
        ]);

        $this->registerCommands();
        $this->registerModuleRoutes();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register artisan commands.
     *
     * @return null
     */
    private function registerCommands()
    {
        $this->app->bind('command.cachekeyprefix', function () {
            return new CacheKeyPrefix(new Filesystem());
        });
        $this->app->bind('command.clearhtml', function () {
            return new ClearHtml();
        });
        $this->app->bind('command.create', function () {
            return new Create(
                new Filesystem()
            );
        });
        $this->app->bind('command.database', function () {
            return new Database(new Filesystem());
        });
        $this->app->bind('command.install', function () {
            return new Install(
                new Filesystem()
            );
        });
        $this->app->bind('command.publish', function () {
            return new Publish(
                new Filesystem()
            );
        });
    }

    /**
     * Get routes from pages.
     *
     * @return null
     */
    private function registerModuleRoutes()
    {
        $this->app->singleton('typicms.routes', function (Application $app) {
            try {
                return $app->make('Pages')->getForRoutes();
            } catch (Exception $e) {
                return [];
            }
        });
    }
}

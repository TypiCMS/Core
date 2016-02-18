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
use TypiCMS\Modules\Users\Models\User;
use TypiCMS\Modules\Users\Repositories\EloquentUser;

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
     * @return void
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
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        /*
         * Register route service provider
         */
        $app->register('TypiCMS\Modules\Core\Providers\RouteServiceProvider');

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
        $app->view->creator('core::admin._sidebar', 'TypiCMS\Modules\Core\Composers\SidebarViewCreator');

        /*
        |--------------------------------------------------------------------------
        | View composers.
        |--------------------------------------------------------------------------
        */
        $app->view->composers([
            'TypiCMS\Modules\Core\Composers\MasterViewComposer' => '*',
            'TypiCMS\Modules\Core\Composers\LocaleComposer'     => '*::public.*',
            'TypiCMS\Modules\Core\Composers\LocalesComposer'    => '*::admin.*',
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
        return [];
    }

    /**
     * Register artisan commands.
     *
     * @return void
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
                new EloquentUser(new User()),
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
     * @return void
     */
    private function registerModuleRoutes()
    {
        $this->app->singleton('typicms.routes', function (Application $app) {
            try {
                return $app->make('TypiCMS\Modules\Pages\Repositories\PageInterface')->getForRoutes();
            } catch (Exception $e) {
                return [];
            }
        });
    }

    /**
     * Register core modules.
     *
     * @return void
     */
    protected function registerCoreModules()
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
        $app->register('TypiCMS\Modules\Dashboard\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Menus\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Sitemap\Providers\ModuleProvider');
        // Pages module needs to be at last for routing to work.
        $app->register('TypiCMS\Modules\Pages\Providers\ModuleProvider');
    }
}

<?php
namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TypiCMS\Modules\Core\Commands\CacheKeyPrefix;
use TypiCMS\Modules\Core\Commands\Database;
use TypiCMS\Modules\Core\Commands\Install;
use TypiCMS\Modules\Core\Services\TypiCMS;
use TypiCMS\Modules\Core\Services\Upload\FileUpload;
use TypiCMS\Modules\Users\Models\User;
use TypiCMS\Modules\Users\Repositories\EloquentUser;

class ModuleProvider extends ServiceProvider {

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
        $this->app->singleton('typicms', function (Application $app) {
            return new TypiCMS;
        });

        /*
        |--------------------------------------------------------------------------
        | TypiCMS upload service.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('upload.file', function (Application $app) {
            return new FileUpload;
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
        |--------------------------------------------------------------------------|
        */
        $app->view->composers([
            'TypiCMS\Modules\Core\Composers\MasterViewComposer' => '*',
            'TypiCMS\Modules\Core\Composers\LocaleComposer' => '*::public.*',
            'TypiCMS\Modules\Core\Composers\LocalesComposer' => ['*::*._form'],
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
     * Register artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->bind('command.install', function (Application $app) {
            return new Install(
                new EloquentUser(new User),
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
     * Get routes from pages.
     *
     * @return void
     */
    private function registerModuleRoutes()
    {
        $this->app->singleton('typicms.routes', function (Application $app) {
            return $app->make('TypiCMS\Modules\Pages\Repositories\PageInterface')->getForRoutes();
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
        // Pages and menulinks need to be at last for routing to work.
        $app->register('TypiCMS\Modules\Menulinks\Providers\ModuleProvider');
        $app->register('TypiCMS\Modules\Pages\Providers\ModuleProvider');
    }

}

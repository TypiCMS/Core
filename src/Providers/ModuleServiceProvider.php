<?php

namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TypiCMS\Modules\Core\Commands\Create;
use TypiCMS\Modules\Core\Commands\CreateUser;
use TypiCMS\Modules\Core\Commands\Database;
use TypiCMS\Modules\Core\Commands\Install;
use TypiCMS\Modules\Core\Commands\Publish;
use TypiCMS\Modules\Core\Commands\PublishTranslations;
use TypiCMS\Modules\Core\Composers\LocaleComposer;
use TypiCMS\Modules\Core\Composers\LocalesComposer;
use TypiCMS\Modules\Core\Composers\MasterViewComposer;
use TypiCMS\Modules\Core\Composers\SidebarViewCreator;
use TypiCMS\Modules\Core\Services\TypiCMS;
use TypiCMS\Modules\Pages\Models\Page;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        $this->loadViewsFrom(__DIR__.'/../../resources/views/', 'core');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/core'),
            __DIR__.'/../../resources/views/errors' => resource_path('views/errors'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../../resources/js' => resource_path('js'),
            __DIR__.'/../../resources/scss' => resource_path('scss'),
        ], 'resources');

        $this->publishes([
            __DIR__.'/../../public' => public_path(),
        ], 'public');

        $this->publishes([
            __DIR__.'/../../database/seeders/DatabaseSeeder.php' => database_path('seeders/DatabaseSeeder.php'),
        ], 'seeders');

        /*
        |--------------------------------------------------------------------------
        | Sidebar view creator.
        |--------------------------------------------------------------------------
        */
        View::creator('core::admin._sidebar', SidebarViewCreator::class);

        /*
        |--------------------------------------------------------------------------
        | View composers.
        |--------------------------------------------------------------------------
        */
        View::composers([
            MasterViewComposer::class => '*',
            LocaleComposer::class => '*::public.*',
            LocalesComposer::class => '*::admin.*',
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Register route service provider.
        |--------------------------------------------------------------------------
        */
        $this->app->register(RouteServiceProvider::class);

        /*
        |--------------------------------------------------------------------------
        | Collection extensions.
        |--------------------------------------------------------------------------
        */
        $this->app->register(CollectionExtensions::class);

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
        $this->app->bind('TypiCMS', TypiCMS::class);

        /*
        |--------------------------------------------------------------------------
        | Disk drivers for original images and crops.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('filesystem.default.driver', function () {
            return $this->app['filesystem.disk']->getDriver();
        });

        /*
        |--------------------------------------------------------------------------
        | Register TypiCMS commands.
        |--------------------------------------------------------------------------
        */
        $this->commands([
            Create::class,
            Database::class,
            Install::class,
            Publish::class,
            CreateUser::class,
            PublishTranslations::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Register TypiCMS routes.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('typicms.routes', function () {
            return Page::with('images', 'documents')
                ->where('module', '!=', null)
                ->get();
        });
    }
}

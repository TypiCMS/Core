<?php

namespace TypiCMS\Modules\Core\Providers;

use Exception;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
use TypiCMS\Modules\Core\Composers\SidebarViewComposer;
use TypiCMS\Modules\Core\Composers\SidebarViewCreator;
use TypiCMS\Modules\Core\Facades\Blocks;
use TypiCMS\Modules\Core\Facades\Files;
use TypiCMS\Modules\Core\Facades\History as HistoryFacade;
use TypiCMS\Modules\Core\Facades\Menulinks;
use TypiCMS\Modules\Core\Facades\Menus;
use TypiCMS\Modules\Core\Facades\Pages;
use TypiCMS\Modules\Core\Facades\PageSections;
use TypiCMS\Modules\Core\Facades\Roles;
use TypiCMS\Modules\Core\Facades\Tags;
use TypiCMS\Modules\Core\Facades\Taxonomies;
use TypiCMS\Modules\Core\Facades\Terms;
use TypiCMS\Modules\Core\Facades\TypiCMS as TypiCMSFacade;
use TypiCMS\Modules\Core\Facades\Users;
use TypiCMS\Modules\Core\Models\Block;
use TypiCMS\Modules\Core\Models\File;
use TypiCMS\Modules\Core\Models\History;
use TypiCMS\Modules\Core\Models\Menu;
use TypiCMS\Modules\Core\Models\Menulink;
use TypiCMS\Modules\Core\Models\Page;
use TypiCMS\Modules\Core\Models\PageSection;
use TypiCMS\Modules\Core\Models\Role;
use TypiCMS\Modules\Core\Models\Setting;
use TypiCMS\Modules\Core\Models\Tag;
use TypiCMS\Modules\Core\Models\Taxonomy;
use TypiCMS\Modules\Core\Models\Term;
use TypiCMS\Modules\Core\Models\User;
use TypiCMS\Modules\Core\Observers\AddToMenuObserver;
use TypiCMS\Modules\Core\Observers\FileObserver;
use TypiCMS\Modules\Core\Observers\HomePageObserver;
use TypiCMS\Modules\Core\Observers\SlugObserver;
use TypiCMS\Modules\Core\Observers\UriObserver;
use TypiCMS\Modules\Core\Services\FileUploader;
use TypiCMS\Modules\Core\Services\TypiCMS;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        /*
         * Get configuration from DB and store it in the container
         */
        $TypiCMSConfig = $this->app->make('Settings')->allToArray();
        $config = $this->app['config']->get('typicms', []);
        $this->app['config']->set('typicms', array_merge($TypiCMSConfig, $config));

        $this->mergeConfigFrom(__DIR__.'/../config/modules.php', 'typicms.modules');

        $this->loadRoutesFrom(__DIR__.'/../routes/blocks.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/core.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/dashboard.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/files.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/history.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/menus.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/roles.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/search.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/settings.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/tags.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/taxonomies.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/translations.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/users.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/pages.php'); // <- Contains a catch all route, so it should stay at last.

        $this->loadViewsFrom(__DIR__.'/../../resources/views/blocks/', 'blocks');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/core/', 'core');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/dashboard/', 'dashboard');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/files/', 'files');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/menus/', 'menus');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/pages/', 'pages');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/roles/', 'roles');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/search/', 'search');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/settings/', 'settings');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/tags/', 'tags');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/taxonomies/', 'taxonomies');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/translations/', 'translations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views/users/', 'users');

        /*
        |--------------------------------------------------------------------------
        | Publish config.
        |--------------------------------------------------------------------------
        */
        $this->publishes([__DIR__.'/../config/typicms.php' => config_path('typicms.php')], 'typicms-config');

        /*
        |--------------------------------------------------------------------------
        | Publish js and scss files.
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__.'/../../resources/js' => resource_path('js'),
            __DIR__.'/../../resources/scss' => resource_path('scss'),
        ], 'typicms-resources');

        /*
        |--------------------------------------------------------------------------
        | Publish lang files.
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__.'/../../lang' => lang_path(),
        ], 'typicms-lang');

        /*
        |--------------------------------------------------------------------------
        | Publish public folder.
        |--------------------------------------------------------------------------
        */
        $this->publishes([__DIR__.'/../../public' => public_path()], 'typicms-public');

        /*
        |--------------------------------------------------------------------------
        | Publish seeders.
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__.'/../../database/seeders/DatabaseSeeder.php' => database_path('seeders/DatabaseSeeder.php'),
            __DIR__.'/../../database/seeders/PageSeeder.php' => database_path('seeders/PageSeeder.php'),
            __DIR__.'/../../database/seeders/MenulinkSeeder.php' => database_path('seeders/MenulinkSeeder.php'),
            __DIR__.'/../../database/seeders/MenuSeeder.php' => database_path('seeders/MenuSeeder.php'),
            __DIR__.'/../../database/seeders/PermissionSeeder.php' => database_path('seeders/PermissionSeeder.php'),
            __DIR__.'/../../database/seeders/RoleSeeder.php' => database_path('seeders/RoleSeeder.php'),
            __DIR__.'/../../database/seeders/RoleHasPermissionsSeeder.php' => database_path('seeders/RoleHasPermissionsSeeder.php'),
            __DIR__.'/../../database/seeders/SettingsSeeder.php' => database_path('seeders/SettingsSeeder.php'),
            __DIR__.'/../../database/seeders/TranslationSeeder.php' => database_path('seeders/TranslationSeeder.php'),
        ], 'typicms-seeders');

        /*
        |--------------------------------------------------------------------------
        | Publish views.
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__.'/../../resources/views/blocks' => resource_path('views/blocks'),
            __DIR__.'/../../resources/views/core' => resource_path('views/core'),
            __DIR__.'/../../resources/views/dashboard' => resource_path('views/dashboard'),
            __DIR__.'/../../resources/views/errors' => resource_path('views/errors'),
            __DIR__.'/../../resources/views/files' => resource_path('views/files'),
            __DIR__.'/../../resources/views/menus' => resource_path('views/menus'),
            __DIR__.'/../../resources/views/pages' => resource_path('views/pages'),
            __DIR__.'/../../resources/views/roles' => resource_path('views/roles'),
            __DIR__.'/../../resources/views/search' => resource_path('views/search'),
            __DIR__.'/../../resources/views/settings' => resource_path('views/settings'),
            __DIR__.'/../../resources/views/tags' => resource_path('views/tags'),
            __DIR__.'/../../resources/views/taxonomies' => resource_path('views/taxonomies'),
            __DIR__.'/../../resources/views/translations' => resource_path('views/translations'),
            __DIR__.'/../../resources/views/users' => resource_path('views/users'),
        ], 'typicms-views');

        /*
        |--------------------------------------------------------------------------
        | Publish migrations.
        |--------------------------------------------------------------------------
        */
        $this->publishes([
            __DIR__.'/../../database/migrations/create_pages_tables.php.stub' => getMigrationFileName('create_pages_tables'),
            __DIR__.'/../../database/migrations/create_blocks_table.php.stub' => getMigrationFileName('create_blocks_table'),
            __DIR__.'/../../database/migrations/create_settings_table.php.stub' => getMigrationFileName('create_settings_table'),
            __DIR__.'/../../database/migrations/create_history_table.php.stub' => getMigrationFileName('create_history_table'),
            __DIR__.'/../../database/migrations/create_users_table.php.stub' => getMigrationFileName('create_users_table'),
            __DIR__.'/../../database/migrations/create_password_resets_table.php.stub' => getMigrationFileName('create_password_resets_table'),
            __DIR__.'/../../database/migrations/create_files_table.php.stub' => getMigrationFileName('create_files_table'),
            __DIR__.'/../../database/migrations/create_model_has_files_table.php.stub' => getMigrationFileName('create_model_has_files_table'),
            __DIR__.'/../../database/migrations/create_menus_tables.php.stub' => getMigrationFileName('create_menus_tables'),
            __DIR__.'/../../database/migrations/create_tags_table.php.stub' => getMigrationFileName('create_tags_table'),
            __DIR__.'/../../database/migrations/create_taxonomies_tables.php.stub' => getMigrationFileName('create_taxonomies_tables'),
            __DIR__.'/../../database/migrations/create_translations_table.php.stub' => getMigrationFileName('create_translations_table'),
        ], 'typicms-migrations');

        /*
        |--------------------------------------------------------------------------
        | Sidebar
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
            SidebarViewComposer::class => 'core::admin._sidebar',
        ]);
        View::composer('search::public.*', function ($view) {
            $view->page = TypiCMSFacade::getPageLinkedToModule('search');
        });
        View::composer('tags::public.*', function ($view) {
            $view->page = TypiCMSFacade::getPageLinkedToModule('tags');
        });

        /*
        |--------------------------------------------------------------------------
        | Observers.
        |--------------------------------------------------------------------------
        */
        File::observe(new FileObserver(new FileUploader()));
        Page::observe(new AddToMenuObserver());
        Page::observe(new HomePageObserver());
        Page::observe(new UriObserver());
        Taxonomy::observe(new SlugObserver());
        Term::observe(new SlugObserver());

        /*
        |--------------------------------------------------------------------------
        | Aliases.
        |--------------------------------------------------------------------------
        */
        AliasLoader::getInstance()->alias('Blocks', Blocks::class);
        AliasLoader::getInstance()->alias('Files', Files::class);
        AliasLoader::getInstance()->alias('History', HistoryFacade::class);
        AliasLoader::getInstance()->alias('Menulinks', Menulinks::class);
        AliasLoader::getInstance()->alias('Menus', Menus::class);
        AliasLoader::getInstance()->alias('Pages', Pages::class);
        AliasLoader::getInstance()->alias('PageSections', PageSections::class);
        AliasLoader::getInstance()->alias('Roles', Roles::class);
        AliasLoader::getInstance()->alias('Tags', Tags::class);
        AliasLoader::getInstance()->alias('Taxonomies', Taxonomies::class);
        AliasLoader::getInstance()->alias('Terms', Terms::class);
        AliasLoader::getInstance()->alias('TypiCMS', TypiCMSFacade::class);
        AliasLoader::getInstance()->alias('Users', Users::class);

        /*
        |--------------------------------------------------------------------------
        | Blade directives.
        |--------------------------------------------------------------------------
        */
        Blade::directive('block', function ($name) {
            return "<?php echo Blocks::render({$name}) ?>";
        });
        Blade::directive('menu', function ($name) {
            return "<?php echo view('menus::public._menu', ['name' => {$name}]) ?>";
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Bindings.
        |--------------------------------------------------------------------------
        */
        $this->app->bind('Blocks', Block::class);
        $this->app->bind('Files', File::class);
        $this->app->bind('History', History::class);
        $this->app->bind('Menulinks', Menulink::class);
        $this->app->bind('Menus', Menu::class);
        $this->app->bind('Pages', Page::class);
        $this->app->bind('PageSections', PageSection::class);
        $this->app->bind('Roles', Role::class);
        $this->app->bind('Settings', Setting::class);
        $this->app->bind('Tags', Tag::class);
        $this->app->bind('Taxonomies', Taxonomy::class);
        $this->app->bind('Terms', Term::class);
        $this->app->bind('TypiCMS', TypiCMS::class);
        $this->app->bind('Users', User::class);

        /*
        |--------------------------------------------------------------------------
        | Disk drivers for images and crops.
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
            CreateUser::class,
            Database::class,
            Install::class,
            Publish::class,
            PublishTranslations::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Register TypiCMS routes.
        |--------------------------------------------------------------------------
        */
        $this->app->singleton('typicms.routes', function () {
            try {
                return Page::with('images', 'documents')
                    ->where('module', '!=', null)
                    ->get();
            } catch (Exception $e) {
                return [];
            }
        });
    }
}

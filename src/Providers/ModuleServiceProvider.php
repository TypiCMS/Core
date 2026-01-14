<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Providers;

use Exception;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TypiCMS\Modules\Core\Commands\Create;
use TypiCMS\Modules\Core\Commands\CreateUser;
use TypiCMS\Modules\Core\Commands\Database;
use TypiCMS\Modules\Core\Commands\Install;
use TypiCMS\Modules\Core\Commands\Publish;
use TypiCMS\Modules\Core\Composers\LocaleComposer;
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

class ModuleServiceProvider extends ServiceProvider
{
    private int $migrationCount = 0;

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            if ($user->isSuperUser()) {
                return true;
            }
        });

        Paginator::defaultView('core::public.pagination.bootstrap-5');
        Paginator::defaultSimpleView('core::public.pagination.simple-bootstrap-5');

        /*
         * Get configuration from DB and store it in the container
         */
        config([
            'typicms' => array_merge(resolve('Settings')->allToArray(), config('typicms', [])),
        ]);

        /*
         * Merge config from the different modules.
         */
        $this->mergeConfigFrom(__DIR__ . '/../config/dashboard.php', 'typicms.modules.dashboard');
        $this->mergeConfigFrom(__DIR__ . '/../config/pages.php', 'typicms.modules.pages');
        $this->mergeConfigFrom(__DIR__ . '/../config/page_sections.php', 'typicms.modules.page_sections');
        $this->mergeConfigFrom(__DIR__ . '/../config/blocks.php', 'typicms.modules.blocks');
        $this->mergeConfigFrom(__DIR__ . '/../config/history.php', 'typicms.modules.history');
        $this->mergeConfigFrom(__DIR__ . '/../config/menus.php', 'typicms.modules.menus');
        $this->mergeConfigFrom(__DIR__ . '/../config/menulinks.php', 'typicms.modules.menulinks');
        $this->mergeConfigFrom(__DIR__ . '/../config/files.php', 'typicms.modules.files');
        $this->mergeConfigFrom(__DIR__ . '/../config/search.php', 'typicms.modules.search');
        $this->mergeConfigFrom(__DIR__ . '/../config/tags.php', 'typicms.modules.tags');
        $this->mergeConfigFrom(__DIR__ . '/../config/taxonomies.php', 'typicms.modules.taxonomies');
        $this->mergeConfigFrom(__DIR__ . '/../config/terms.php', 'typicms.modules.terms');
        $this->mergeConfigFrom(__DIR__ . '/../config/translations.php', 'typicms.modules.translations');
        $this->mergeConfigFrom(__DIR__ . '/../config/users.php', 'typicms.modules.users');
        $this->mergeConfigFrom(__DIR__ . '/../config/roles.php', 'typicms.modules.roles');

        /*
         * Load routes.
         */
        $this->loadRoutesFrom(__DIR__ . '/../routes/blocks.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/core.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/dashboard.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/files.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/history.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/menus.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/roles.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/search.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/settings.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/sitemap.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/tags.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/taxonomies.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/translations.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/users.php');

        /*
         * Load views.
         */
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/blocks/', 'blocks');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/core/', 'core');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/dashboard/', 'dashboard');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/files/', 'files');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/menus/', 'menus');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/pages/', 'pages');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/roles/', 'roles');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/search/', 'search');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/settings/', 'settings');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/tags/', 'tags');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/taxonomies/', 'taxonomies');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/translations/', 'translations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/users/', 'users');

        /*
         |--------------------------------------------------------------------------
         | Publish config.
         |--------------------------------------------------------------------------
         */
        $this->publishes([__DIR__ . '/../config/typicms.php' => config_path('typicms.php')], 'typicms-config');

        /*
         |--------------------------------------------------------------------------
         | Publish images, fonts, js and scss files.
         |--------------------------------------------------------------------------
         */
        $this->publishes([
            __DIR__ . '/../../resources/images' => resource_path('images'),
            __DIR__ . '/../../resources/fonts' => resource_path('fonts'),
            __DIR__ . '/../../resources/js' => resource_path('js'),
            __DIR__ . '/../../resources/scss' => resource_path('scss'),
        ], 'typicms-resources');

        /*
         |--------------------------------------------------------------------------
         | Publish lang files.
         |--------------------------------------------------------------------------
         */
        $this->publishes([
            __DIR__ . '/../../lang' => lang_path(),
        ], 'typicms-lang');

        /*
         |--------------------------------------------------------------------------
         | Publish public folder.
         |--------------------------------------------------------------------------
         */
        $this->publishes([__DIR__ . '/../../public' => public_path()], 'typicms-public');

        /*
         |--------------------------------------------------------------------------
         | Publish seeders.
         |--------------------------------------------------------------------------
         */
        $this->publishes([
            __DIR__ . '/../../database/seeders/DatabaseSeeder.php' => database_path('seeders/DatabaseSeeder.php'),
            __DIR__ . '/../../database/seeders/PageSeeder.php' => database_path('seeders/PageSeeder.php'),
            __DIR__ . '/../../database/seeders/MenuSeeder.php' => database_path('seeders/MenuSeeder.php'),
            __DIR__ . '/../../database/seeders/PermissionSeeder.php' => database_path('seeders/PermissionSeeder.php'),
            __DIR__ . '/../../database/seeders/RoleSeeder.php' => database_path('seeders/RoleSeeder.php'),
            __DIR__ . '/../../database/seeders/RoleHasPermissionsSeeder.php' => database_path(
                'seeders/RoleHasPermissionsSeeder.php',
            ),
            __DIR__ . '/../../database/seeders/SettingsSeeder.php' => database_path('seeders/SettingsSeeder.php'),
            __DIR__ . '/../../database/seeders/TranslationSeeder.php' => database_path('seeders/TranslationSeeder.php'),
        ], 'typicms-seeders');

        /*
         |--------------------------------------------------------------------------
         | Publish views.
         |--------------------------------------------------------------------------
         */
        $this->publishes([
            __DIR__ . '/../../resources/views/errors' => resource_path('views/errors'),
            __DIR__ . '/../../resources/views/blocks' => resource_path('views/vendor/blocks'),
            __DIR__ . '/../../resources/views/core' => resource_path('views/vendor/core'),
            __DIR__ . '/../../resources/views/dashboard' => resource_path('views/vendor/dashboard'),
            __DIR__ . '/../../resources/views/files' => resource_path('views/vendor/files'),
            __DIR__ . '/../../resources/views/menus' => resource_path('views/vendor/menus'),
            __DIR__ . '/../../resources/views/pages' => resource_path('views/vendor/pages'),
            __DIR__ . '/../../resources/views/roles' => resource_path('views/vendor/roles'),
            __DIR__ . '/../../resources/views/search' => resource_path('views/vendor/search'),
            __DIR__ . '/../../resources/views/settings' => resource_path('views/vendor/settings'),
            __DIR__ . '/../../resources/views/tags' => resource_path('views/vendor/tags'),
            __DIR__ . '/../../resources/views/taxonomies' => resource_path('views/vendor/taxonomies'),
            __DIR__ . '/../../resources/views/translations' => resource_path('views/vendor/translations'),
            __DIR__ . '/../../resources/views/users' => resource_path('views/vendor/users'),
        ], 'typicms-views');

        /*
         |--------------------------------------------------------------------------
         | Publish migrations.
         |--------------------------------------------------------------------------
         */
        $this->publishes([
            __DIR__ . '/../../database/migrations/create_files_table.php.stub' => $this->getMigrationFileName(
                'create_files_table',
            ),
            __DIR__ . '/../../database/migrations/create_pages_tables.php.stub' => $this->getMigrationFileName(
                'create_pages_tables',
            ),
            __DIR__ . '/../../database/migrations/create_blocks_table.php.stub' => $this->getMigrationFileName(
                'create_blocks_table',
            ),
            __DIR__ . '/../../database/migrations/create_settings_table.php.stub' => $this->getMigrationFileName(
                'create_settings_table',
            ),
            __DIR__ . '/../../database/migrations/create_history_table.php.stub' => $this->getMigrationFileName(
                'create_history_table',
            ),
            __DIR__ . '/../../database/migrations/create_users_table.php.stub' => $this->getMigrationFileName(
                'create_users_table',
            ),
            __DIR__
                . '/../../database/migrations/create_one_time_passwords_table.php.stub' => $this->getMigrationFileName(
                'create_one_time_passwords_table',
            ),
            __DIR__ . '/../../database/migrations/create_passkeys_table.php.stub' => $this->getMigrationFileName(
                'create_passkeys_table',
            ),
            __DIR__ . '/../../database/migrations/create_model_has_files_table.php.stub' => $this->getMigrationFileName(
                'create_model_has_files_table',
            ),
            __DIR__ . '/../../database/migrations/create_menus_tables.php.stub' => $this->getMigrationFileName(
                'create_menus_tables',
            ),
            __DIR__ . '/../../database/migrations/create_tags_table.php.stub' => $this->getMigrationFileName(
                'create_tags_table',
            ),
            __DIR__ . '/../../database/migrations/create_taxonomies_tables.php.stub' => $this->getMigrationFileName(
                'create_taxonomies_tables',
            ),
            __DIR__ . '/../../database/migrations/create_translations_table.php.stub' => $this->getMigrationFileName(
                'create_translations_table',
            ),
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
            SidebarViewComposer::class => 'core::admin._sidebar',
        ]);
        View::composer('search::public.*', function ($view): void {
            $view->page = getPageLinkedToModule('search');
        });
        View::composer('tags::public.*', function ($view): void {
            $view->page = getPageLinkedToModule('tags');
        });

        Blade::componentNamespace('TypiCMS\\Modules\\Core\\Http\\Components', 'core');

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
        AliasLoader::getInstance()->alias('Users', Users::class);

        /*
         |--------------------------------------------------------------------------
         | Blade directives.
         |--------------------------------------------------------------------------
         */
        Blade::directive('block', fn (string $name): string => sprintf('<?php echo Blocks::render(%s) ?>', $name));
        Blade::directive('menu', fn (string $name): string => sprintf(
            "<?php echo view('menus::public._menu', ['name' => %s]) ?>",
            $name,
        ));
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
        $this->app->bind('Users', User::class);

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
        ]);

        /*
         |--------------------------------------------------------------------------
         | Register TypiCMS routes.
         |--------------------------------------------------------------------------
         */
        $this->app->singleton('typicms.routes', function (): array {
            try {
                return Page::query()
                    ->with('images', 'documents')
                    ->whereNotNull('module')
                    ->get()
                    ->all();
            } catch (Exception) {
                return [];
            }
        });
    }

    private function getMigrationFileName(string $name): string
    {
        return getMigrationFileName($name, ++$this->migrationCount);
    }
}

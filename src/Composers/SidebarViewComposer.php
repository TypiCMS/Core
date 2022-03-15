<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Sidebar\SidebarGroup;
use Maatwebsite\Sidebar\SidebarItem;

class SidebarViewComposer
{
    public function compose(View $view)
    {
        if (Gate::allows('see dashboard')) {
            $view->sidebar->group('dashboard', function (SidebarGroup $group) {
                $group->id = 'dashboard';
                $group->weight = 10;
                $group->hideHeading();
                $group->addItem(__('Dashboard'), function (SidebarItem $item) {
                    $item->id = 'dashboard';
                    $item->icon = config('typicms.modules.dashboard.sidebar.icon');
                    $item->weight = config('typicms.modules.dashboard.sidebar.weight');
                    $item->route('admin::dashboard');
                });
            });
        }
        if (Gate::allows('read pages')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Pages'), function (SidebarItem $item) {
                    $item->id = 'pages';
                    $item->icon = config('typicms.modules.pages.sidebar.icon');
                    $item->weight = config('typicms.modules.pages.sidebar.weight');
                    $item->route('admin::index-pages');
                    $item->append('admin::create-page');
                });
            });
        }
        if (Gate::allows('read menus')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Menus'), function (SidebarItem $item) {
                    $item->id = 'menus';
                    $item->icon = config('typicms.modules.menus.sidebar.icon');
                    $item->weight = config('typicms.modules.menus.sidebar.weight');
                    $item->route('admin::index-menus');
                    $item->append('admin::create-menu');
                });
            });
        }
        if (Gate::allows('read blocks')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Content blocks'), function (SidebarItem $item) {
                    $item->id = 'blocks';
                    $item->icon = config('typicms.modules.blocks.sidebar.icon');
                    $item->weight = config('typicms.modules.blocks.sidebar.weight');
                    $item->route('admin::index-blocks');
                    $item->append('admin::create-block');
                });
            });
        }
        if (Gate::allows('read tags')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Tags'), function (SidebarItem $item) {
                    $item->id = 'tags';
                    $item->icon = config('typicms.modules.tags.sidebar.icon');
                    $item->weight = config('typicms.modules.tags.sidebar.weight');
                    $item->route('admin::index-tags');
                    $item->append('admin::create-tag');
                });
            });
        }
        if (Gate::allows('read taxonomies')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Taxonomies'), function (SidebarItem $item) {
                    $item->id = 'taxonomies';
                    $item->icon = config('typicms.modules.taxonomies.sidebar.icon');
                    $item->weight = config('typicms.modules.taxonomies.sidebar.weight');
                    $item->route('admin::index-taxonomies');
                    $item->append('admin::create-taxonomy');
                });
            });
        }
        if (Gate::allows('read translations')) {
            $view->sidebar->group(__('Content'), function (SidebarGroup $group) {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__('Translations'), function (SidebarItem $item) {
                    $item->id = 'translations';
                    $item->icon = config('typicms.modules.translations.sidebar.icon');
                    $item->weight = config('typicms.modules.translations.sidebar.weight');
                    $item->route('admin::index-translations');
                    $item->append('admin::create-translation');
                });
            });
        }
        if (Gate::allows('read users')) {
            $view->sidebar->group(__('Users and roles'), function (SidebarGroup $group) {
                $group->id = 'users';
                $group->weight = 50;
                $group->addItem(__('Users'), function (SidebarItem $item) {
                    $item->id = 'users';
                    $item->icon = config('typicms.modules.users.sidebar.icon');
                    $item->weight = config('typicms.modules.users.sidebar.weight');
                    $item->route('admin::index-users');
                    $item->append('admin::create-user');
                });
            });
        }
        if (Gate::allows('read roles')) {
            $view->sidebar->group(__('Users and roles'), function (SidebarGroup $group) {
                $group->id = 'users';
                $group->weight = 50;
                $group->addItem(__('Roles'), function (SidebarItem $item) {
                    $item->id = 'roles';
                    $item->icon = config('typicms.modules.roles.sidebar.icon');
                    $item->weight = config('typicms.modules.roles.sidebar.weight');
                    $item->route('admin::index-roles');
                    $item->append('admin::create-role');
                });
            });
        }
        if (Gate::allows('read files')) {
            $view->sidebar->group(__('Media'), function (SidebarGroup $group) {
                $group->id = 'media';
                $group->weight = 40;
                $group->addItem(__('Files'), function (SidebarItem $item) {
                    $item->id = 'files';
                    $item->icon = config('typicms.modules.files.sidebar.icon');
                    $item->weight = config('typicms.modules.files.sidebar.weight');
                    $item->route('admin::index-files');
                });
            });
        }
    }
}

<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use TypiCMS\Modules\Sidebar\SidebarGroup;
use TypiCMS\Modules\Sidebar\SidebarItem;

class SidebarViewComposer
{
    public function compose(View $view): void
    {
        if (Gate::allows('see dashboard')) {
            $view->offsetGet('sidebar')->group('dashboard', function (SidebarGroup $group): void {
                $group->id = 'dashboard';
                $group->weight = 10;
                $group->hideHeading();
                $group->addItem(__(config('typicms.modules.dashboard.sidebar.label', 'Dashboard')), function (SidebarItem $item): void {
                    $item->id = 'dashboard';
                    $item->icon = config('typicms.modules.dashboard.sidebar.icon');
                    $item->weight = config('typicms.modules.dashboard.sidebar.weight');
                    $item->route('admin::dashboard');
                });
            });
        }
        if (Gate::allows('read pages')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.pages.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.pages.sidebar.label', 'Pages')), function (SidebarItem $item): void {
                    $item->id = 'pages';
                    $item->icon = config('typicms.modules.pages.sidebar.icon');
                    $item->weight = config('typicms.modules.pages.sidebar.weight');
                    $item->route('admin::index-pages');
                });
            });
        }
        if (Gate::allows('read menus')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.menus.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.menus.sidebar.label', 'Menus')), function (SidebarItem $item): void {
                    $item->id = 'menus';
                    $item->icon = config('typicms.modules.menus.sidebar.icon');
                    $item->weight = config('typicms.modules.menus.sidebar.weight');
                    $item->route('admin::index-menus');
                });
            });
        }
        if (Gate::allows('read blocks')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.blocks.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.blocks.sidebar.label', 'Content blocks')), function (SidebarItem $item): void {
                    $item->id = 'blocks';
                    $item->icon = config('typicms.modules.blocks.sidebar.icon');
                    $item->weight = config('typicms.modules.blocks.sidebar.weight');
                    $item->route('admin::index-blocks');
                });
            });
        }
        if (Gate::allows('read tags')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.tags.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.tags.sidebar.label', 'Tags')), function (SidebarItem $item): void {
                    $item->id = 'tags';
                    $item->icon = config('typicms.modules.tags.sidebar.icon');
                    $item->weight = config('typicms.modules.tags.sidebar.weight');
                    $item->route('admin::index-tags');
                });
            });
        }
        if (Gate::allows('read taxonomies')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.taxonomies.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.taxonomies.sidebar.label', 'Taxonomies')), function (SidebarItem $item): void {
                    $item->id = 'taxonomies';
                    $item->icon = config('typicms.modules.taxonomies.sidebar.icon');
                    $item->weight = config('typicms.modules.taxonomies.sidebar.weight');
                    $item->route('admin::index-taxonomies');
                });
            });
        }
        if (Gate::allows('read translations')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.translations.sidebar.group', 'Content')), function (SidebarGroup $group): void {
                $group->id = 'content';
                $group->weight = 30;
                $group->addItem(__(config('typicms.modules.translations.sidebar.label', 'Translations')), function (SidebarItem $item): void {
                    $item->id = 'translations';
                    $item->icon = config('typicms.modules.translations.sidebar.icon');
                    $item->weight = config('typicms.modules.translations.sidebar.weight');
                    $item->route('admin::index-translations');
                });
            });
        }
        if (Gate::allows('read users')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.users.sidebar.group', 'Users and roles')), function (SidebarGroup $group): void {
                $group->id = 'users';
                $group->weight = 50;
                $group->addItem(__(config('typicms.modules.users.sidebar.label', 'Users')), function (SidebarItem $item): void {
                    $item->id = 'users';
                    $item->icon = config('typicms.modules.users.sidebar.icon');
                    $item->weight = config('typicms.modules.users.sidebar.weight');
                    $item->route('admin::index-users');
                });
            });
        }
        if (Gate::allows('read roles')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.roles.sidebar.group', 'Users and roles')), function (SidebarGroup $group): void {
                $group->id = 'users';
                $group->weight = 50;
                $group->addItem(__(config('typicms.modules.roles.sidebar.label', 'Roles')), function (SidebarItem $item): void {
                    $item->id = 'roles';
                    $item->icon = config('typicms.modules.roles.sidebar.icon');
                    $item->weight = config('typicms.modules.roles.sidebar.weight');
                    $item->route('admin::index-roles');
                });
            });
        }
        if (Gate::allows('read files')) {
            $view->offsetGet('sidebar')->group(__(config('typicms.modules.files.sidebar.group', 'Media')), function (SidebarGroup $group): void {
                $group->id = 'media';
                $group->weight = 40;
                $group->addItem(__(config('typicms.modules.files.sidebar.label', 'Files')), function (SidebarItem $item): void {
                    $item->id = 'files';
                    $item->icon = config('typicms.modules.files.sidebar.icon');
                    $item->weight = config('typicms.modules.files.sidebar.weight');
                    $item->route('admin::index-files');
                });
            });
        }
    }
}

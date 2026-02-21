<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HasAdminUrls
{
    public function editUrl(): string
    {
        $route = 'admin::edit-' . Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->id);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::index-' . $this->getTable();
        if (Route::has($route)) {
            return route($route);
        }

        return route('admin::dashboard');
    }
}

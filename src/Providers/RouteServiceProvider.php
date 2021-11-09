<?php

namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\LocaleController;

class RouteServiceProvider extends ServiceProvider
{
    public function map(): void
    {
        /*
         * Admin routes
         */
        Route::middleware('admin')->prefix('admin')->name('admin::')->group(function (Router $router) {
            $router->get('_locale/{locale}', [LocaleController::class, 'setContentLocale'])->name('change-locale');
        });
    }
}

<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\DashboardAdminController;

/*
 * Admin routes
 */
Route::middleware('admin')->prefix('admin')->name('admin::')->group(function (Router $router) {
    $router->get('dashboard', [DashboardAdminController::class, 'dashboard'])->name('dashboard')->middleware('can:see dashboard');
    $router->get('', [DashboardAdminController::class, 'index'])->name('index')->middleware('can:see dashboard');
});

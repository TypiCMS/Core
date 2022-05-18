<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\SettingsAdminController;

/*
 * Admin routes
 */
Route::middleware('admin')->prefix('admin')->name('admin::')->group(function (Router $router) {
    $router->get('settings', [SettingsAdminController::class, 'index'])->name('index-settings')->middleware('can:read settings');
    $router->post('settings', [SettingsAdminController::class, 'save'])->name('update-settings')->middleware('can:update settings');
    $router->get('cache/clear', [SettingsAdminController::class, 'clearCache'])->name('clear-cache')->middleware('can:clear cache');
    $router->patch('settings', [SettingsAdminController::class, 'deleteImage'])->name('delete-image-in-settings')->middleware('can:update settings');
});

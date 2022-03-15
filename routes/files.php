<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\FilesAdminController;
use TypiCMS\Modules\Core\Http\Controllers\FilesApiController;

/*
 * Admin routes
 */
Route::middleware('admin')->prefix('admin')->name('admin::')->group(function (Router $router) {
    $router->get('files', [FilesAdminController::class, 'index'])->name('index-files')->middleware('can:read files');
    $router->get('files/{file}/edit', [FilesAdminController::class, 'edit'])->name('edit-file')->middleware('can:read files');
    $router->put('files/{file}', [FilesAdminController::class, 'update'])->name('update-file')->middleware('can:update files');
});

/*
 * API routes
 */
Route::middleware(['api', 'auth:api'])->prefix('api')->group(function (Router $router) {
    $router->get('files', [FilesApiController::class, 'index'])->middleware('can:read files');
    $router->post('files', [FilesApiController::class, 'store'])->middleware('can:create files');
    $router->patch('files/{ids}', [FilesApiController::class, 'move'])->middleware('can:update files');
    $router->delete('files/{file}', [FilesApiController::class, 'destroy'])->middleware('can:delete files');
});

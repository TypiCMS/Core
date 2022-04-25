<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\HistoryApiController;

/*
 * API routes
 */
Route::middleware(['api', 'auth:api'])->prefix('api')->group(function (Router $router) {
    $router->get('history', [HistoryApiController::class, 'index'])->middleware('can:see history');
    $router->delete('history', [HistoryApiController::class, 'destroy'])->middleware('can:clear history');
});

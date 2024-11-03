<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\SearchPublicController;

/*
 * Front office routes
 */
if ($page = getPageLinkedToModule('search')) {
    $middleware = $page->private ? ['public', 'auth'] : ['public'];
    foreach (locales() as $lang) {
        if ($page->isPublished($lang) && $path = $page->path($lang)) {
            Route::middleware($middleware)->prefix($path)->name($lang . '::')->group(function (Router $router) {
                $router->get('/', [SearchPublicController::class, 'search'])->name('search');
            });
        }
    }
}

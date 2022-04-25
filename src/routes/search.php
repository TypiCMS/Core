<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Http\Controllers\SearchPublicController;

/*
 * Front office routes
 */
if ($page = TypiCMS::getPageLinkedToModule('search')) {
    $middleware = $page->private ? ['public', 'auth'] : ['public'];
    foreach (locales() as $lang) {
        if ($page->isPublished($lang) && $uri = $page->uri($lang)) {
            Route::middleware($middleware)->prefix($uri)->name($lang.'::')->group(function (Router $router) {
                $router->get('/', [SearchPublicController::class, 'search'])->name('search');
            });
        }
    }
}

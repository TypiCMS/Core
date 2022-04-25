<?php

use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\SitemapPublicController;

/*
 * Front office routes
 */
Route::get('sitemap.xml', [SitemapPublicController::class, 'generate'])->name('sitemap');

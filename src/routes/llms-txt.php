<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\LlmsTxtController;

/*
 * Front office routes
 */
Route::get('llms.txt', LlmsTxtController::class)->name('llms-txt');

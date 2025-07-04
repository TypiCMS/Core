<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Http\Controllers\AuthController;
use TypiCMS\Modules\Core\Http\Controllers\ImpersonateController;
use TypiCMS\Modules\Core\Http\Controllers\PasskeysApiController;
use TypiCMS\Modules\Core\Http\Controllers\RegisterController;
use TypiCMS\Modules\Core\Http\Controllers\UsersAdminController;
use TypiCMS\Modules\Core\Http\Controllers\UsersApiController;
use TypiCMS\Modules\Core\Http\Middleware\JavaScriptData;

/*
 * Front office routes
 */
foreach (locales() as $lang) {
    Route::middleware(['public', JavaScriptData::class])->prefix($lang)->name($lang . '::')->group(function (Router $router) {
        if (config('typicms.registration.allowed')) {
            // Registration
            $router->get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
            $router->post('register', [RegisterController::class, 'register'])->name('register-action');
        }
        // Login with a passkey
        $router->get('login', [AuthController::class, 'showPasskeyLoginForm'])->name('login');
        // Login with one-time password
        $router->get('otp-login', [AuthController::class, 'showOneTimePasswordLoginForm'])->name('otp-login');
        $router->post('otp-login', [AuthController::class, 'submitOneTimePasswordLoginForm'])->name('send-one-time-password');
        $router->get('otp-login-code', [AuthController::class, 'showOneTimePasswordForm'])->name('login-code');
        $router->post('otp-login-code', [AuthController::class, 'submitOneTimePassword'])->name('submit-one-time-password');
        $router->middleware('auth')->group(function (Router $router) {
            // Require passkey creation
            $router->get('create-passkey', [AuthController::class, 'showPasskeyCreationForm'])->name('create-passkey');
        });
        // Logout
        $router->post('logout', [AuthController::class, 'logout'])->name('logout');
        // Impersonate
        $router->get('stop-impersonation', [ImpersonateController::class, 'stopImpersonation'])->name('stop-impersonation');
    });
}

Route::middleware('web')->group(function (Router $router) {
    Route::passkeys();
});

/*
 * Admin routes
 */
Route::middleware('admin')->prefix('admin')->name('admin::')->group(function (Router $router) {
    $router->get('users', [UsersAdminController::class, 'index'])->name('index-users')->middleware('can:read users');
    $router->get('users/export', [UsersAdminController::class, 'export'])->name('export-users')->middleware('can:read users');
    $router->get('users/create', [UsersAdminController::class, 'create'])->name('create-user')->middleware('can:create users');
    $router->get('users/{user}/edit', [UsersAdminController::class, 'edit'])->name('edit-user')->middleware('can:read users');
    $router->post('users', [UsersAdminController::class, 'store'])->name('store-user')->middleware('can:create users');
    $router->put('users/{user}', [UsersAdminController::class, 'update'])->name('update-user')->middleware('can:update users');
    $router->get('users/{id}/impersonate', [ImpersonateController::class, 'start'])->name('impersonate-user')->middleware('can:impersonate users');
});

/*
 * API routes
 */
Route::middleware(['api', 'auth:api'])->prefix('api')->group(function (Router $router) {
    $router->get('users', [UsersApiController::class, 'index'])->middleware('can:read users');
    $router->post('users/current/update-preferences', [UsersApiController::class, 'updatePreferences'])->middleware('can:update users');
    $router->delete('users/{user}', [UsersApiController::class, 'destroy'])->middleware('can:delete users');
    // Passkeys API routes
    $router->delete('passkeys/{passkey}', [PasskeysApiController::class, 'destroy'])->middleware('can:update users');
    $router->post('passkeys', [PasskeysApiController::class, 'store'])->middleware('can:update users');
    $router->get('passkeys/generate-options', [PasskeysApiController::class, 'generatePasskeyOptions'])->middleware('can:update users');
    $router->get('users/{user}/passkeys', [PasskeysApiController::class, 'getPasskeys'])->middleware('can:read users');
});

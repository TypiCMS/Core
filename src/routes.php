<?php
/**
 * Register routes filter classes
 */
Route::filter('public.checkLocale',   'TypiCMS\Filters\PublicFilter@checkLocale');
Route::filter('admin.setLocale',      'TypiCMS\Filters\AdminFilter@setLocale');
Route::filter('visitor.publicAccess', 'TypiCMS\Filters\UsersFilter@publicAccess');
Route::filter('visitor.mayRegister',  'TypiCMS\Filters\UsersFilter@mayRegister');
Route::filter('user.auth',            'TypiCMS\Filters\UsersFilter@auth');
Route::filter('user.hasAccess',       'TypiCMS\Filters\UsersFilter@hasAccess');
Route::filter('user.inGroup',         'TypiCMS\Filters\UsersFilter@inGroup');

/**
 * Route filter "public"
 */
foreach (Config::get('translatable.locales') as $locale) {
    Route::when($locale,      'public.checkLocale');
    Route::when($locale.'/*', 'public.checkLocale');
}

/**
 * Route filter back office
 */
Route::when('admin',   'admin.setLocale|user.auth|user.hasAccess');
Route::when('admin/*', 'admin.setLocale|user.auth|user.hasAccess');
Route::when('api',     'admin.setLocale');
Route::when('api/*',   'admin.setLocale');
Route::when('api',     'user.auth|user.hasAccess', ['post', 'put', 'patch', 'delete']);
Route::when('api/*',   'user.auth|user.hasAccess', ['post', 'put', 'patch', 'delete']);

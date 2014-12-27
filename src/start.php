<?php

View::addNamespace('core', __DIR__ . '/views/');
Lang::addNamespace('core', __DIR__ . '/lang/');
Config::addNamespace('core', __DIR__ . '/config/');

App::error(function(Exception $exception, $code)
{
	// Throw 404 error on ModelNotFoundException
	if ($exception instanceof Illuminate\Database\Eloquent\ModelNotFoundException) {
		$code = 404;
	}

	switch ($code)
	{
		case 403:
			return Response::view(
				'core::errors.403', [
					'title' => 'Erreur 403 – ' . Config::get('typicms.' . App::getLocale() . '.websiteTitle'),
					'lang' => App::getLocale(),
					'bodyClass' => 'error-403'
				], $code
			);

		case 404:
			return Response::view(
				'core::errors.404', [
					'title' => 'Erreur 404 – ' . Config::get('typicms.' . App::getLocale() . '.websiteTitle'),
					'lang' => App::getLocale(),
					'bodyClass' => 'error-404'
				], $code
			);
	}
	// return Response::view(
	// 	'core::errors.500', [
	// 		'title' => 'Erreur 500 – ' . Config::get('typicms.' . App::getLocale() . '.websiteTitle'), 
	// 		'lang' => App::getLocale(),
	// 		'bodyClass' => 'error-500'
	// 	], 500
	// );
});

/*
|--------------------------------------------------------------------------
| New Relic app name
|--------------------------------------------------------------------------
*/

if (extension_loaded('newrelic')) {
	newrelic_set_appname('');
}

/*
|--------------------------------------------------------------------------
| HTML macros.
|--------------------------------------------------------------------------|
*/

require __DIR__ . '/Macros.php';

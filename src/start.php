<?php

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
	if (! App::isLocal()) {
		return Response::view(
			'errors.500', [
				'title' => 'Erreur 500 – ' . Config::get('typicms.' . App::getLocale() . '.websiteTitle'), 
				'lang' => App::getLocale(),
				'bodyClass' => 'error-500'
			], 500
		);
	}
});

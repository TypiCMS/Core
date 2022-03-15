<?php

namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;
use TypiCMS\Modules\Core\Loaders\MixedLoader;

class TranslationsServiceProvider extends LaravelTranslationServiceProvider
{
    /**
     * Register the translation line loader.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new MixedLoader($app['files'], $app['path.lang']);
        });
    }
}

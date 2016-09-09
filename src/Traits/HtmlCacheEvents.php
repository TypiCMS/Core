<?php

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Support\Facades\Artisan;

trait HtmlCacheEvents
{
    /**
     * Event to delete files in public/html folder.
     *
     * @return null
     */
    public static function bootHtmlCacheEvents()
    {
        if (config('typicms.html_cache')) {
            static::saved(function ($model) {
                Artisan::call('clear-html');
            });

            static::deleted(function ($model) {
                Artisan::call('clear-html');
            });
        }
    }
}

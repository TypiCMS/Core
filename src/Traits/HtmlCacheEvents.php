<?php
namespace TypiCMS\Traits;

use Illuminate\Support\Facades\File;

trait HtmlCacheEvents {

    /**
     * Event to delete files in public/cache folder
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (config('typicms.html_cache')) {

            static::saved(function($model) {
                File::deleteDirectory(public_path() . '/cache/html');
            });

            static::deleted(function($model) {
                File::deleteDirectory(public_path() . '/cache/html');
            });

        }
    }

}

<?php

namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionExtensions extends ServiceProvider
{
    public function boot()
    {
        Collection::macro('translate', function ($locale = null) {
            $locale = $locale ?: app()->getLocale();

            return collect($this->items)->map(function ($item) use ($locale) {
                foreach ($item->getAttributes() as $key => $value) {
                    if (in_array($key, $item->translatable)) {
                        $item->$key = $item->translate($key, $locale);
                    }
                }

                return $item;
            });
        });
    }

    public function register()
    {
    }
}

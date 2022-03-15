<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Models\Page;

class SitemapPublicController extends Controller
{
    public function generate()
    {
        // create new sitemap object
        $sitemap = app('sitemap');

        // set cache (key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean))
        // by default cache is disabled
        if (config('typicms.cache')) {
            $sitemap->setCache('laravel.sitemap', 3600);
        }

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached()) {
            foreach (TypiCMS::enabledLocales() as $locale) {
                app()->setLocale($locale);

                $pages = Page::published()->where('private', 0)->get();

                foreach ($pages as $page) {
                    $url = url($page->uri($locale));
                    $sitemap->add($url, $page->updated_at);

                    if (!$module = ucfirst($page->module)) {
                        continue;
                    }

                    if (!class_exists($module)) {
                        continue;
                    }

                    if (!Route::has($locale.'::'.Str::singular(mb_strtolower($module)))) {
                        continue;
                    }

                    foreach ($module::published()->get() as $item) {
                        $url = url($item->uri($locale));
                        $sitemap->add($url, $item->updated_at);
                    }
                }
            }
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');
    }
}

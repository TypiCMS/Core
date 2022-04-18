<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Models\Page;

class PagesPublicController extends BasePublicController
{
    /**
     * Page uri : lang/slug.
     *
     * @param null|mixed $uri
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function uri($uri = null)
    {
        $page = $this->findPageByUri($uri);

        if ($page->private && !Auth::check()) {
            return redirect()->guest(route(app()->getLocale().'::login'));
        }

        if ($page->redirect && $page->publishedSubpages->count() > 0) {
            $childUri = $page->publishedSubpages->first()->uri();

            return redirect($childUri);
        }

        // get submenu
        $children = $page->getSubPages();

        $templateDir = 'pages::'.config('typicms.template_dir', 'public').'.';
        $template = $page->template ?: 'default';

        if (!view()->exists($templateDir.$template)) {
            info('Template '.$template.' not found, switching to default template.');
            $template = 'default';
        }

        return view($templateDir.$template, compact('children', 'page'));
    }

    /**
     * Find page by URI.
     */
    private function findPageByUri(?string $uri): Page
    {
        $query = Page::published()
            ->with([
                'image',
                'images',
                'documents',
                'publishedSections.image',
                'publishedSections.images',
                'publishedSections.documents',
            ]);

        if ($uri === null) {
            return $query->where('is_home', 1)->firstOrFail();
        }

        // Only locale in url
        if (
            in_array($uri, TypiCMS::enabledLocales())
            && (
                TypiCMS::mainLocale() !== $uri
                || config('typicms.main_locale_in_url')
            )
        ) {
            return $query->where('is_home', 1)->firstOrFail();
        }

        $query->published();

        $query->whereUriIs($uri);

        return $query->firstOrFail();
    }

    /**
     * Get browser language or default locale and redirect to homepage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToHomepage()
    {
        $homepage = Page::published()->where('is_home', 1)->firstOrFail();
        $locale = $this->getBrowserLanguageOrDefault();

        return redirect($homepage->uri($locale));
    }

    /**
     * Get browser language or app.locale.
     *
     * @return string
     */
    private function getBrowserLanguageOrDefault()
    {
        if ($browserLanguage = getenv('HTTP_ACCEPT_LANGUAGE')) {
            $browserLocale = mb_substr($browserLanguage, 0, 2);
            if (in_array($browserLocale, TypiCMS::enabledLocales())) {
                return $browserLocale;
            }
        }

        return config('app.locale');
    }

    /**
     * Display the lang chooser.
     */
    public function langChooser()
    {
        $homepage = Page::published()->where('is_home', 1)->first();
        if (!$homepage) {
            app('log')->error('No homepage found.');
            abort(404);
        }
        $locales = TypiCMS::enabledLocales();

        return view('core::public.lang-chooser')
            ->with(compact('homepage', 'locales'));
    }
}

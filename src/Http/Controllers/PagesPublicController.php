<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Models\Page;

class PagesPublicController extends BasePublicController
{
    public function uri(?string $uri = null): RedirectResponse|View
    {
        $page = $this->findPageByUri($uri);

        if ($page->private && !Auth::check()) {
            return redirect()->guest(route(app()->getLocale().'::login'));
        }

        if ($page->redirect && $page->publishedSubpages->count() > 0) {
            $childUri = $page->publishedSubpages->first()->uri();

            return redirect($childUri);
        }

        // Get subpages.
        $children = $page->getSubPages();

        $templateDir = 'pages::'.config('typicms.template_dir', 'public').'.';
        $template = $page->template ?: 'default';

        if (!view()->exists($templateDir.$template)) {
            info('Template '.$template.' not found, switching to default template.');
            $template = 'default';
        }

        return view($templateDir.$template, compact('children', 'page'));
    }

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

    public function redirectToHomepage(): RedirectResponse
    {
        $homepage = Page::published()->where('is_home', 1)->firstOrFail();
        $locale = $this->getBrowserLanguageOrDefault();

        return redirect($homepage->uri($locale));
    }

    private function getBrowserLanguageOrDefault(): string
    {
        if ($locale = mb_substr(getenv('HTTP_ACCEPT_LANGUAGE'), 0, 2)) {
            if (in_array($locale, TypiCMS::enabledLocales())) {
                return $locale;
            }
        }

        return TypiCMS::mainLocale();
    }

    public function langChooser(): View
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

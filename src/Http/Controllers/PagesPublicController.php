<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Models\Page;

class PagesPublicController extends BasePublicController
{
    public function uri(?string $uri = null): RedirectResponse|View
    {
        $page = $this->findPageByUri($uri);

        if ($page->private && !Auth::check()) {
            return redirect()->guest(route(app()->getLocale() . '::login'));
        }

        if ($page->redirect && $page->publishedSubpages->count() > 0) {
            $child = $page->publishedSubpages->first();
            if ($child instanceof Page) {
                return redirect()->to($child->url());
            }
        }

        $templateDir = 'pages::' . config('typicms.template_dir', 'public') . '.';
        $template = $page->template ?: 'default';

        if (!view()->exists($templateDir . $template)) {
            info('Template ' . $template . ' not found, switching to default template.');
            $template = 'default';
        }

        return view($templateDir . $template, compact('page', 'templateDir'));
    }

    private function findPageByUri(?string $uri): Page
    {
        $query = Page::query()->published()
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
            in_array($uri, enabledLocales())
            && (
                mainLocale() !== $uri
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
        $homepage = Page::query()->published()->where('is_home', 1)->firstOrFail();
        $locale = getBrowserLocaleOrMainLocale();

        return redirect($homepage->url($locale));
    }

    public function langChooser(): View
    {
        $homepage = Page::query()->published()->where('is_home', 1)->first();
        if (!$homepage) {
            app('log')->error('No homepage found.');
            abort(404);
        }
        $locales = enabledLocales();

        return view('core::public.lang-chooser')
            ->with(compact('homepage', 'locales'));
    }
}

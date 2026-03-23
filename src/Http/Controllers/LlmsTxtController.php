<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\MarkdownResponse\Attributes\DoNotProvideMarkdown;
use TypiCMS\Modules\Core\Models\Menu;
use TypiCMS\Modules\Core\Models\Menulink;
use TypiCMS\Modules\Core\Models\Page;

final class LlmsTxtController extends Controller
{
    #[DoNotProvideMarkdown]
    public function __invoke(): Response
    {
        $content = Cache::remember('llms-txt', 3600, fn (): string => $this->generateContent());

        return response($content, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    private function generateContent(): string
    {
        $locale = 'en';
        app()->setLocale($locale);

        $lines = [];

        $lines[] = '## Key Pages';
        $lines[] = '';

        $sectionPages = $this->getSectionPages($locale);
        foreach ($sectionPages as $label => $url) {
            $lines[] = "- [{$label}]({$url})";
        }

        $lines[] = '';

        // $lines = $this->addNews($lines, $locale);

        return implode("\n", $lines);
    }

    /** @return array<string, string> */
    private function getSectionPages(string $locale): array
    {
        $pages = [];

        /** @var Page|null $homePage */
        $homePage = Page::query()
            ->published()
            ->where('is_home', true)
            ->first();

        if ($homePage) {
            $pages['Home'] = $homePage->url($locale);
        }

        $primaryMenu = Menu::query()
            ->published()
            ->where('name', 'primary')
            ->first();

        if ($primaryMenu) {
            $menulinks = Menulink::query()
                ->where('menu_id', $primaryMenu->id)
                ->whereNull('parent_id')
                ->published()
                ->orderBy('position')
                ->with('page')
                ->get();

            foreach ($menulinks as $menulink) {
                $page = $menulink->page;
                if ($page instanceof Page && $page->isPublished($locale)) {
                    $title = $page->translate('title', $locale);
                    if ($title) {
                        $pages[$title] = $page->url($locale);
                    }
                }
            }
        }

        $audiencePages = Page::query()
            ->published()
            ->where('template', 'landing-audience')
            ->orderBy('position')
            ->get();

        foreach ($audiencePages as $page) {
            $title = $page->translate('title', $locale);
            if ($title) {
                $cleanTitle = preg_replace('/\*([^*]+)\*/', '$1', (string) $title);
                $pages[$cleanTitle] = $page->url($locale);
            }
        }

        $modules = ['news'];
        foreach ($modules as $module) {
            $page = getPageLinkedToModule($module);
            if ($page instanceof Page && $page->isPublished($locale)) {
                $pages[$page->title] = $page->url($locale);
            }
        }

        return $pages;
    }
}

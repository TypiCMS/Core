<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;
use TypiCMS\Modules\Core\Models\Page;

class UriObserver
{
    /**
     * On create, update uri.
     */
    public function creating(Page $page)
    {
        $slugs = $page->getTranslations('slug');
        foreach ($slugs as $locale => $slug) {
            $uri = $this->incrementWhileExists($page, $slug, $locale);
            $page->setTranslation('uri', $locale, $uri);
        }
    }

    /**
     * On update, change uri.
     */
    public function updating(Page $page)
    {
        $parentUris = $this->getParentUris($page);
        $uris = [];

        foreach (locales() as $locale) {
            $slug = $page->getTranslation('slug', $locale);
            $parentUri = $parentUris[$locale] ?? '';
            if (!empty($parentUri)) {
                $uri = $parentUri;
                if (!empty($slug)) {
                    $uri .= '/'.$slug;
                }
            } else {
                $uri = $slug;
            }
            $uri = $this->incrementWhileExists($page, $uri, $locale, $page->id);
            $uris[$locale] = $uri;
        }
        $page->uri = $uris;
    }

    /**
     * On updated, empty child uri.
     */
    public function updated(Model $page)
    {
        $this->emptySubpagesUris($page);
    }

    /**
     * Recursive method for emptying subpages URIs
     * UriObserver will rebuild the URIs.
     */
    private function emptySubpagesUris(Model $page): void
    {
        foreach ($page->subpages as $subpage) {
            $uris = $subpage->getTranslations('uri');
            foreach ($uris as $locale => $uri) {
                $subpage->setTranslation('uri', $locale, null);
            }
            $subpage->save();
            $this->emptySubpagesUris($subpage);
        }
    }

    /**
     * Get the URIs of the parent page.
     */
    private function getParentUris(Page $page): array
    {
        if ($page->parent !== null) {
            return $page->parent->getTranslations('uri');
        }

        return [];
    }

    /**
     * Check if the uri exists.
     */
    private function uriExists(Page $page, string $uri, string $locale, int $id = null): bool
    {
        $query = $page->where('uri->'.$locale, $uri);
        if ($id !== null) {
            $query->where('id', '!=', $id);
        }

        return $query->count() > 0;
    }

    /**
     * Add '-x' on uri if it exists in pages table.
     */
    private function incrementWhileExists(Page $page, string $uri, string $locale, int $id = null): string
    {
        if (empty($uri)) {
            return '';
        }

        $originalUri = $uri;

        $i = 0;
        // Check if uri is unique
        while ($this->uriExists($page, $uri, $locale, $id)) {
            ++$i;
            // increment uri if it exists
            $uri = $originalUri.'-'.$i;
        }

        return $uri;
    }
}

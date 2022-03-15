<?php

namespace TypiCMS\Modules\Core\Presenters;

class PagePresenter extends Presenter
{
    /**
     * Get Uri without last segment.
     */
    public function parentUri(string $locale): string
    {
        $parentUri = $this->entity->translate('uri', $locale) ?: '/';
        $parentUri = explode('/', $parentUri);
        array_pop($parentUri);
        $parentUri = implode('/', $parentUri).'/';

        return $parentUri;
    }
}

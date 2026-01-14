<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Presenters;

class TranslationsPresenter extends Presenter
{
    /**
     * Return name.
     */
    public function title(): string
    {
        return $this->entity->key;
    }
}

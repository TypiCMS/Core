<?php

namespace TypiCMS\Modules\Core\Presenters;

class BlockPresenter extends Presenter
{
    /**
     * Get title.
     */
    public function title(): string
    {
        return $this->entity->name;
    }
}

<?php

namespace TypiCMS\Modules\Core\Presenters;

class MenusPresenter extends Presenter
{
    /**
     * Get title.
     */
    public function title(): string
    {
        return $this->entity->name;
    }
}

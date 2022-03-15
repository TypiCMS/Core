<?php

namespace TypiCMS\Modules\Core\Presenters;

class TagsModulePresenter extends Presenter
{
    /**
     * Get title.
     */
    public function title(): string
    {
        return $this->entity->tag;
    }
}

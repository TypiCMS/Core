<?php

namespace TypiCMS\Modules\Core\Presenters;

class TagsModulePresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->tag;
    }
}

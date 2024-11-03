<?php

namespace TypiCMS\Modules\Core\Presenters;

class MenusPresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->name;
    }
}

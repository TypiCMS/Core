<?php

namespace TypiCMS\Modules\Core\Presenters;

class MenulinkPresenter extends Presenter
{
    public function menuclass(): string
    {
        return $this->entity->menuclass;
    }
}

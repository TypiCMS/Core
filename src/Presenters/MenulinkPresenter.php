<?php

namespace TypiCMS\Modules\Core\Presenters;

class MenulinkPresenter extends Presenter
{
    public function menuclass()
    {
        return $this->entity->menuclass;
    }
}

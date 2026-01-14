<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Presenters;

class MenulinkPresenter extends Presenter
{
    public function menuclass(): string
    {
        return $this->entity->menuclass;
    }
}

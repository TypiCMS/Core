<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Presenters;

class RolePresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->name;
    }
}

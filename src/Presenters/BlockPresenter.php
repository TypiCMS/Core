<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Presenters;

class BlockPresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->name;
    }
}

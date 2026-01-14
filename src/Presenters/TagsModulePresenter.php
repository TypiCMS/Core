<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Presenters;

class TagsModulePresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->tag;
    }
}

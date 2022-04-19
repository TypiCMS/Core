<?php

namespace TypiCMS\Modules\Core\Presenters;

class TaxonomyPresenter extends Presenter
{
    public function title(): string
    {
        return $this->entity->name ?? '';
    }
}

<?php

namespace TypiCMS\Modules\Core\Presenters;

class UsersPresenter extends Presenter
{
    /**
     * Get title by concatenating first_name and last_name.
     */
    public function title(): string
    {
        return $this->entity->first_name.' '.$this->entity->last_name;
    }
}

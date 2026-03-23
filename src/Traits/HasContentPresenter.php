<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

trait HasContentPresenter
{
    public function presentTitle(): string
    {
        if ($this->id === null) {
            return '';
        }

        return strip_tags((string) $this->title);
    }
}

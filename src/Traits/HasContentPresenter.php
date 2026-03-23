<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

trait HasContentPresenter
{
    public function presentTitle(): string
    {
        if (!$this->exists) {
            return '';
        }

        return strip_tags((string) $this->title);
    }
}

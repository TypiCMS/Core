<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Support\Facades\Vite;

trait HasOgImagePresenter
{
    use HasImagePresenter;

    public function ogImageUrl(): string
    {
        if ($this->ogImage !== '') {
            return $this->imageUrl(1200, 630, [], 'ogImage');
        }

        if ($this->image !== '') {
            return $this->imageUrl(1200, 630);
        }

        return Vite::asset(config('typicms.og_image'));
    }
}

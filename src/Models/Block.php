<?php

namespace TypiCMS\Modules\Core\Models;

use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\BlockPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

class Block extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = BlockPresenter::class;

    protected $guarded = [];

    public array $translatable = [
        'status',
        'body',
    ];

    public function render($name = null)
    {
        $args = func_get_args();
        $args[] = app()->getLocale();

        $block = $this->where('name', $name)
            ->published()
            ->first();

        return $block !== null ? $block->present()->body : '';
    }
}

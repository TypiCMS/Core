<?php

namespace TypiCMS\Modules\Core\Models;

use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Traits\Historable;

class Block extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected $presenter = 'TypiCMS\Modules\Core\Presenters\BlockPresenter';

    protected $guarded = [];

    public $translatable = [
        'status',
        'body',
    ];

    public function render($name = null)
    {
        $args = func_get_args();
        $args[] = config('app.locale');

        $block = $this->where('name', $name)
            ->published()
            ->first();

        return $block !== null ? $block->present()->body : '';
    }
}

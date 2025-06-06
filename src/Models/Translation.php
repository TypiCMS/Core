<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\TranslationsPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property-read int $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Translation extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = TranslationsPresenter::class;

    protected $guarded = [];

    public array $translatable = [
        'translation',
    ];
}

<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\TranslationsPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property int $id
 * @property string $key
 * @property array<array-key, mixed> $translation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-write mixed $status
 * @property-read mixed $translations
 */
class Translation extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = TranslationsPresenter::class;

    protected $guarded = [];

    /** @var array<string> */
    public array $translatable = [
        'translation',
    ];
}

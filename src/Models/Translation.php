<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\TranslationsPresenter;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Modules\Core\Traits\Publishable;
use TypiCMS\Translatable\HasTranslations;

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
class Translation extends Model
{
    use Cachable;
    use HasAdminUrls;
    use HasConfigurableOrder;
    use HasSelectableFields;
    use HasSlugScope;
    use HasTranslations;
    use Historable;
    use PresentableTrait;
    use Publishable;

    protected string $presenter = TranslationsPresenter::class;

    protected $guarded = [];

    /** @var array<string> */
    public array $translatable = [
        'translation',
    ];
}

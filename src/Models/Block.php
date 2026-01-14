<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Observers\TipTapHTMLObserver;
use TypiCMS\Modules\Core\Presenters\BlockPresenter;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed> $status
 * @property array<array-key, mixed> $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-read mixed $translations
 */
#[ObservedBy([TipTapHTMLObserver::class])]
class Block extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = BlockPresenter::class;

    protected $guarded = [];

    /** @var array<string> */
    public array $translatable = [
        'status',
        'body',
    ];

    /** @var array<string> */
    public array $tipTapContent = [
        'body',
    ];

    public function render(?string $name = null): string
    {
        $args = func_get_args();
        $args[] = app()->getLocale();

        $block = self::query()
            ->where('name', $name)
            ->published()
            ->first();

        return $block !== null ? $block->present()->body : '';
    }
}

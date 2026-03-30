<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use TypiCMS\Modules\Core\Observers\TipTapHTMLObserver;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasBodyPresenter;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasContentPresenter;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Modules\Core\Traits\Publishable;
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
#[Unguarded]
class Block extends Model
{
    use HasAdminUrls;
    use HasBodyPresenter;
    use HasConfigurableOrder;
    use HasContentPresenter;
    use HasSelectableFields;
    use HasSlugScope;
    use HasTranslations;
    use Historable;
    use Publishable;

    /** @var array<string> */
    public array $translatable = [
        'status',
        'body',
    ];

    /** @var array<string> */
    public array $tipTapContent = [
        'body',
    ];

    public function presentTitle(): string
    {
        return $this->name;
    }

    public function render(?string $name = null): string
    {
        $block = static::query()
            ->where('name', $name)
            ->published()
            ->first();

        return $block !== null ? $block->formattedBody() : '';
    }
}

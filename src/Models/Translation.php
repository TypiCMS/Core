<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Historable;
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
#[Unguarded]
class Translation extends Model
{
    use HasAdminUrls;
    use HasConfigurableOrder;
    use HasSelectableFields;
    use HasSlugScope;
    use HasTranslations;
    use Historable;

    public function presentTitle(): string
    {
        return $this->key;
    }

    /** @var array<string> */
    public array $translatable = [
        'translation',
    ];
}

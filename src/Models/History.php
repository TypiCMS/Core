<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\HistoryPresenter;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Publishable;

/**
 * @property int $id
 * @property int $historable_id
 * @property string $historable_type
 * @property int|null $user_id
 * @property string|null $title
 * @property string|null $locale
 * @property string $historable_table
 * @property string $action
 * @property object $old
 * @property object $new
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ?Model $historable
 * @property-read mixed $href
 * @property-write mixed $status
 * @property-read User|null $user
 */
class History extends Model
{
    use Cachable;
    use HasAdminUrls;
    use HasConfigurableOrder;
    use HasSelectableFields;
    use HasSlugScope;
    use PresentableTrait;
    use Publishable;

    protected $table = 'history';

    protected string $presenter = HistoryPresenter::class;

    protected $guarded = [];

    protected $appends = ['href'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'old' => 'object',
            'new' => 'object',
        ];
    }

    public string $order = 'id';

    public string $direction = 'desc';

    /** @return MorphTo<Model, $this> */
    public function historable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return Attribute<string, null> */
    protected function href(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->historable === null) {
                return null;
            }

            return method_exists($this->historable, 'editUrl') ? $this->historable->editUrl() : '';
        });
    }
}

<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\HistoryPresenter;

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
 * @property-read Model|\Eloquent $historable
 * @property-read mixed $href
 * @property-write mixed $status
 * @property-read User|null $user
 */
class History extends Base
{
    use PresentableTrait;

    protected $table = 'history';

    protected string $presenter = HistoryPresenter::class;

    protected $guarded = [];

    protected $appends = ['href'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old' => 'object',
            'new' => 'object',
        ];
    }

    public string $order = 'id';

    public string $direction = 'desc';

    public function historable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function href(): Attribute
    {
        $href = null;
        if ($this->historable) {
            $href = $this->historable->editUrl();
        }

        return Attribute::make(
            get: fn () => $href,
        );
    }
}

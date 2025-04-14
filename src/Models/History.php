<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\HistoryPresenter;

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

        return new Attribute(
            get: fn () => $href,
        );
    }
}

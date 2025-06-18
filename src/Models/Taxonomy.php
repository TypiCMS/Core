<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Observers\SlugObserver;
use TypiCMS\Modules\Core\Presenters\TaxonomyPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

#[ObservedBy(SlugObserver::class)]
class Taxonomy extends Base implements Sortable
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;
    use SortableTrait;

    protected string $presenter = TaxonomyPresenter::class;

    protected $guarded = [];

    public array $translatable = [
        'title',
        'slug',
        'result_string',
    ];

    public $sortable = [
        'order_column_name' => 'position',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'modules' => 'array',
        ];
    }

    public function allForSelect(): array
    {
        $items = self::query()
            ->order()
            ->get()
            ->pluck('title', 'id')
            ->all();

        return ['' => ''] + $items;
    }

    public function url($locale = null): string
    {
        return '/';
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->order();
    }
}

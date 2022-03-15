<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\TaxonomyPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

class Taxonomy extends Base implements Sortable
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;
    use SortableTrait;

    protected $presenter = TaxonomyPresenter::class;

    protected $guarded = [];

    public $translatable = [
        'title',
        'slug',
        'result_string',
    ];

    public $sortable = [
        'order_column_name' => 'position',
    ];

    public $casts = [
        'modules' => 'array',
    ];

    public function allForSelect(): array
    {
        $items = $this->order()
            ->get()
            ->pluck('title', 'id')
            ->all();

        return ['' => ''] + $items;
    }

    public function uri($locale = null): string
    {
        return '/';
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->order();
    }
}

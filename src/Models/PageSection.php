<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Laracasts\Presenter\PresentableTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\PagePresenter;
use TypiCMS\Modules\Core\Traits\HasFiles;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property int $id
 * @property int $page_id
 * @property int|null $image_id
 * @property int $position
 * @property string|null $template
 * @property string $status
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PageSection extends Base implements Sortable
{
    use HasFiles;
    use HasTranslations;
    use Historable;
    use PresentableTrait;
    use SortableTrait;

    protected string $presenter = PagePresenter::class;

    protected $guarded = [];

    protected $appends = ['thumb'];

    public array $translatable = [
        'title',
        'slug',
        'status',
        'body',
    ];

    public $sortable = [
        'order_column_name' => 'position',
    ];

    public function buildSortQuery()
    {
        return static::query()->where('page_id', $this->page_id);
    }

    protected function thumb(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->present()->image(null, 54),
        );
    }

    public function url($locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $this->page->url($locale) . '#' . $this->position . '-' . $this->translate('slug', $locale);
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-page_section';
        if (Route::has($route)) {
            return route($route, [$this->page_id, $this->id]);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::edit-page';
        if (Route::has($route)) {
            return route($route, $this->page_id);
        }

        return route('admin::dashboard');
    }

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** @return BelongsTo<File, $this> */
    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }
}

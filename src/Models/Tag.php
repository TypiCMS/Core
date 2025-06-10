<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\TagsModulePresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property int $id
 * @property string $tag
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-write mixed $status
 */
class Tag extends Base
{
    use Historable;
    use PresentableTrait;

    protected string $presenter = TagsModulePresenter::class;

    protected $guarded = [];

    #[Scope]
    protected function published(Builder $query): void {}

    public function url($locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $route = $locale . '::tag';
        $slug = $this->slug ?: null;

        return Route::has($route) && $slug ? url(route($route, $slug)) : url('/');
    }
}

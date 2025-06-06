<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\TagsModulePresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property-read int $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
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
        $slug = $this->translate('slug', $locale) ?: null;

        return Route::has($route) && $slug ? url(route($route, $slug)) : url('/');
    }
}

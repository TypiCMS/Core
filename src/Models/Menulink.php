<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\NestableTrait;

class Menulink extends Base
{
    use HasTranslations;
    use Historable;
    use NestableTrait;
    use PresentableTrait;

    protected $presenter = 'TypiCMS\Modules\Core\Presenters\MenulinkPresenter';

    protected $guarded = [];

    public $translatable = [
        'title',
        'url',
        'description',
        'status',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class);
    }

    public function submenulinks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->order();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-menulink';
        if (Route::has($route)) {
            return route($route, [$this->menu_id, $this->id]);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::edit-menu';
        if (Route::has($route)) {
            return route($route, $this->menu_id);
        }

        return route('admin::dashboard');
    }
}

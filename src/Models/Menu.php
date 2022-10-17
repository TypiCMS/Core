<?php

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\MenusPresenter;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\NestableCollection;

class Menu extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected $presenter = MenusPresenter::class;

    protected $guarded = [];

    protected $appends = ['thumb'];

    public $translatable = [
        'status',
    ];

    public function getMenu($name): ?self
    {
        try {
            $menu = $this->published()
                ->with([
                    'menulinks' => function ($query) {
                        $query->with([
                            'page',
                            'section',
                            'image',
                            'parent',
                        ])->published();
                    },
                ])
                ->where('name', $name)
                ->first();

            $menu->menulinks = $this->prepare($menu->menulinks)->nest();

            return $menu;
        } catch (Exception $e) {
            Log::info('No menu found with name “'.$name.'”');

            return null;
        }
    }

    public function prepare($items = null): NestableCollection
    {
        $items->each(function ($item) {
            $item->items = collect();
            $item->href = $this->setHref($item);
            $item->class = $this->setClass($item);
        });

        return $items;
    }

    /**
     * Set the href to the menulink’s URL or the URL of the linked page.
     */
    public function setHref(Menulink $menulink): string
    {
        if ($menulink->url) {
            return $menulink->url;
        }
        if ($menulink->page) {
            if ($menulink->section !== null) {
                return $menulink->page->uri().'#'.$menulink->section->slug.'-'.$menulink->section->id;
            }

            return $menulink->page->uri();
        }

        return '/';
    }

    /**
     * Set the class and add active if needed.
     *
     * @param mixed $menulink
     */
    public function setClass($menulink): string
    {
        $classArray = preg_split('/ /', $menulink->class, -1, PREG_SPLIT_NO_EMPTY);
        // add active class if current uri is equal to item uri or contains
        // item uri and is bigger than 3 to avoid homepage link always active ('/', '/lg')
        $pattern = $menulink->href;
        if (mb_strlen($menulink->href) > 3) {
            $pattern .= '*';
        }
        if (request()->is($pattern)) {
            $classArray[] = 'active';
        }

        return implode(' ', $classArray);
    }

    protected function thumb(): Attribute
    {
        return new Attribute(
            get: fn () => $this->present()->image(null, 54),
        );
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(\TypiCMS\Modules\Core\Models\File::class, 'image_id');
    }

    public function menulinks(): HasMany
    {
        return $this->hasMany(Menulink::class)->orderBy('position', 'asc');
    }
}

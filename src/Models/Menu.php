<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\MenusPresenter;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\NestableCollection;
use TypiCMS\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int|null $image_id
 * @property string $name
 * @property string|null $class
 * @property array<array-key, mixed> $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-read File|null $image
 * @property-read NestableCollection<int, Menulink> $menulinks
 * @property-read int|null $menulinks_count
 * @property-read mixed $thumb
 * @property-read mixed $translations
 */
class Menu extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = MenusPresenter::class;

    protected $guarded = [];

    protected $appends = ['thumb'];

    /** @var array<string> */
    public array $translatable = [
        'status',
    ];

    public function getMenu(string $name): ?self
    {
        try {
            $menu = self::query()
                ->published()
                ->with([
                    'menulinks' => function ($query): void {
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
        } catch (Exception) {
            Log::info('No menu found with name “' . $name . '”');

            return null;
        }
    }

    public function prepare(?NestableCollection $items = null): NestableCollection
    {
        $items->each(function (Model $item, int $key): void {
            /** @var Menulink $item */
            $item->items = new Collection();
            $item->href = $this->setHref($item);
            $item->class = $this->setClass($item);
        });

        return $items;
    }

    /**
     * Set the href to the menu link’s website or the URL of the related page.
     */
    public function setHref(Menulink $menulink): string
    {
        if ($menulink->website) {
            return $menulink->website;
        }

        if ($menulink->page) {
            $url = $menulink->page->path();
            if ($menulink->section) {
                return $url . '#' . $menulink->section->slug . '-' . $menulink->section->id;
            }

            return $url;
        }

        return '/';
    }

    /**
     * Set the class and add active if needed.
     */
    public function setClass(Menulink $menulink): string
    {
        $classArray = (array) preg_split('/ /', (string) $menulink->class, -1, PREG_SPLIT_NO_EMPTY);
        // add active class if current uri is equal to item uri or contains
        // item uri and is bigger than 3 to avoid homepage link always active ('/', '/lg')
        $pattern = $menulink->href;
        if (mb_strlen((string) $menulink->href) > 3) {
            $pattern .= '*';
        }

        if (request()->is($pattern)) {
            $classArray[] = 'active';
        }

        return implode(' ', $classArray);
    }

    /** @return Attribute<string, null> */
    protected function thumb(): Attribute
    {
        return Attribute::make(get: fn () => $this->present()->image(null, 54));
    }

    /** @return BelongsTo<File, $this> */
    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    /** @return HasMany<Menulink, $this> */
    public function menulinks(): HasMany
    {
        return $this->hasMany(Menulink::class)->orderBy('position', 'asc');
    }
}

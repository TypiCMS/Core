<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Observers\AddToMenuObserver;
use TypiCMS\Modules\Core\Observers\HomePageObserver;
use TypiCMS\Modules\Core\Observers\UriObserver;
use TypiCMS\Modules\Core\Presenters\PagePresenter;
use TypiCMS\Modules\Core\Traits\HasFiles;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\NestableCollection;
use TypiCMS\NestableTrait;

#[ObservedBy([AddToMenuObserver::class, HomePageObserver::class, UriObserver::class])]
class Page extends Base
{
    use HasFiles;
    use HasTranslations;
    use Historable;
    use NestableTrait;
    use PresentableTrait;

    protected string $presenter = PagePresenter::class;

    protected $guarded = [];

    protected $casts = [
        'is_home' => 'boolean',
        'private' => 'boolean',
        'redirect' => 'boolean',
    ];

    public array $translatable = [
        'title',
        'slug',
        'uri',
        'status',
        'body',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function uri($locale = null): string
    {
        $locale = $locale ?: config('app.locale');
        $uri = $this->translate('uri', $locale);
        if (
            mainLocale() !== $locale
            || config('typicms.main_locale_in_url')
        ) {
            $uri = $uri ? $locale . '/' . $uri : $locale;
        }

        return $uri ?: '/';
    }

    public function isHome(): bool
    {
        return $this->is_home;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function scopeWhereUriIs($query, $uri): Builder
    {
        $field = 'uri';
        if (in_array($field, $this->translatable)) {
            $field .= '->' . config('app.locale');
        }

        return $query->where($field, $uri);
    }

    public function scopeWhereUriIsNot($query, $uri): Builder
    {
        $field = 'uri';
        if (in_array($field, $this->translatable)) {
            $field .= '->' . config('app.locale');
        }

        return $query->where($field, '!=', $uri);
    }

    public function scopeWhereUriIsLike($query, $uri): Builder
    {
        $field = 'uri';
        if (in_array($field, $this->translatable)) {
            $field .= '->' . config('app.locale');
        }

        return $query->where($field, 'LIKE', $uri);
    }

    public function allForSelect(): array
    {
        $pages = $this->order()
            ->get()
            ->nest()
            ->listsFlattened();

        return ['' => ''] + array_map('strip_tags', $pages);
    }

    public function getSubPages(): NestableCollection
    {
        $rootUriArray = explode('/', $this->uri);
        $uri = $rootUriArray[0];
        if (in_array($uri, locales())) {
            if (isset($rootUriArray[1])) {
                $uri .= '/' . $rootUriArray[1]; // add next part of uri in locale
            }
        }

        return $this->whereUriIsNot($uri)
            ->published()
            ->orderBy('position')
            ->whereUriIsLike($uri . '%')
            ->get()
            ->noCleaning()
            ->nest();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->order();
    }

    public function publishedSections(): HasMany
    {
        return $this->sections()->published();
    }

    public function menulinks(): HasMany
    {
        return $this->hasMany(Menulink::class);
    }

    public function subpages(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->order();
    }

    public function publishedSubpages(): HasMany
    {
        return $this->subpages()->published();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }
}

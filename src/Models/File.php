<?php

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\FilePresenter;
use TypiCMS\Modules\Core\Traits\Historable;

class File extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected $presenter = FilePresenter::class;

    protected $guarded = [];

    public $translatable = [
        'description',
        'alt_attribute',
    ];

    protected $appends = ['thumb_sm', 'alt_attribute_translated', 'url'];

    public function getAltAttributeTranslatedAttribute(): string
    {
        $locale = config('app.locale');

        return $this->translate('alt_attribute', config('typicms.content_locale', $locale));
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'folder_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'folder_id', 'id');
    }

    public function getThumbSmAttribute(): string
    {
        return $this->present()->image(240, 240, ['resize']);
    }

    public function getUrlAttribute(): string
    {
        $url = '';

        try {
            $url = Storage::url($this->path);
        } catch (Exception $e) {
        }

        return $url;
    }
}

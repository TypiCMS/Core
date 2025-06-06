<?php

namespace TypiCMS\Modules\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Translatable\HasTranslations;
use TypiCMS\Modules\Core\Presenters\FilePresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property-read int $id
 * @property string $path
 * @property-read string $thumb_sm
 * @property-read string $url
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class File extends Base
{
    use HasTranslations;
    use Historable;
    use PresentableTrait;

    protected string $presenter = FilePresenter::class;

    protected $guarded = [];

    public array $translatable = [
        'title',
        'description',
        'alt_attribute',
    ];

    protected $appends = ['thumb_sm', 'url'];

    protected function thumbSm(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->present()->image(240, 240, ['resize']),
        );
    }

    protected function url(): Attribute
    {
        $url = '';

        try {
            $url = Storage::url($this->path);
        } catch (Exception $e) {
        }

        return Attribute::make(
            get: fn () => $url
        );
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'folder_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'folder_id', 'id');
    }
}

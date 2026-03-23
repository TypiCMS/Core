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
use Illuminate\Support\Facades\Storage;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasContentPresenter;
use TypiCMS\Modules\Core\Traits\HasImagePresenter;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int|null $folder_id
 * @property string|null $type
 * @property string|null $name
 * @property string|null $path
 * @property string|null $extension
 * @property string|null $mimetype
 * @property int|null $width
 * @property int|null $height
 * @property int|null $filesize
 * @property string $title
 * @property string $description
 * @property string $alt_attribute
 * @property int|null $focal_x
 * @property int|null $focal_y
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, File> $children
 * @property-read int|null $children_count
 * @property-read File|null $folder
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-write mixed $status
 * @property-read mixed $thumb_sm
 * @property-read mixed $translations
 * @property-read mixed $url
 */
class File extends Model
{
    use HasAdminUrls;
    use HasConfigurableOrder;
    use HasContentPresenter;
    use HasImagePresenter;
    use HasSelectableFields;
    use HasSlugScope;
    use HasTranslations;
    use Historable;

    protected $guarded = [];

    /** @var array<string> */
    public array $translatable = [
        'title',
        'description',
        'alt_attribute',
    ];

    protected $appends = ['thumb_sm', 'url'];

    public function presentTitle(): string
    {
        return $this->title ?: $this->name;
    }

    public function formattedFilesize(int $precision = 0): string
    {
        $base = log($this->filesize, 1024);
        $suffixes = ['', __('KB'), __('MB'), __('GB'), __('TB')];

        return round(1024 ** ($base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    protected function getImagePathOrDefault(string $relationName): string
    {
        $imagePath = $this->path ?? '';

        if (!Storage::exists($imagePath)) {
            return $this->imgNotFound();
        }

        return $imagePath;
    }

    /** @return Attribute<string, null> */
    protected function thumbSm(): Attribute
    {
        return Attribute::make(get: fn () => $this->imageUrl(240, 240, ['resize']));
    }

    /** @return Attribute<string, null> */
    protected function url(): Attribute
    {
        $url = '';

        try {
            $url = Storage::url($this->path);
        } catch (Exception) {
        }

        return Attribute::make(get: fn () => $url);
    }

    /** @return BelongsTo<File, $this> */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'folder_id', 'id');
    }

    /** @return HasMany<File, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'folder_id', 'id');
    }
}

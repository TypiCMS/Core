<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Bkwld\Croppa\Facades\Croppa;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait HasImagePresenter
{
    protected string $imageNotFound = 'img-not-found.png';

    protected function getImagePathOrDefault(string $relationName): string
    {
        $path = '';

        if (is_object($this->{$relationName})) {
            $path = $this->{$relationName}->path;
        }

        if (!is_file(Storage::path($path))) {
            return $this->imgNotFound();
        }

        return $path;
    }

    /**
     * Return URL of a resized or cropped image.
     *
     * @param array<string|int, string|array<string>> $options
     */
    public function imageUrl(
        ?int $width = null,
        ?int $height = null,
        array $options = [],
        string $relationName = 'image',
    ): string {
        $path = $this->getImagePathOrDefault($relationName);

        if (in_array(pathinfo($path, PATHINFO_EXTENSION), ['svg', 'gif'], true)) {
            return Storage::url($path);
        }

        return url(Croppa::url('storage/' . $path, $width, $height, $options));
    }

    public function imgNotFound(): string
    {
        if (!Storage::exists($this->imageNotFound)) {
            Storage::put($this->imageNotFound, File::get(resource_path('images/' . $this->imageNotFound)));
        }

        return $this->imageNotFound;
    }
}

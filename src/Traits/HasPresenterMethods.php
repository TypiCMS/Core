<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Bkwld\Croppa\Facades\Croppa;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

trait HasPresenterMethods
{
    protected string $imageNotFound = 'img-not-found.png';

    public function dateLocalized(string $column = 'date', string $format = 'LL'): string
    {
        return $this->{$column}->isoFormat($format);
    }

    public function dateTimeLocalized(string $column = 'datetime', string $format = 'LLL'): string
    {
        return $this->{$column}->isoFormat($format);
    }

    public function datetimeOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('Y-m-d\TH:i');
    }

    public function dateOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('Y-m-d');
    }

    public function timeOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('H:i');
    }

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

    public function ogImageUrl(): string
    {
        if ($this->ogImage !== '') {
            return $this->imageUrl(1200, 630, [], 'ogImage');
        }

        if ($this->image !== '') {
            return $this->imageUrl(1200, 630);
        }

        return Vite::asset(config('typicms.og_image'));
    }

    public function presentTitle(): string
    {
        if ($this->id === null) {
            return '';
        }

        return strip_tags((string) $this->title);
    }

    public function formattedBody(): string
    {
        return $this->dynamicLinks();
    }

    public function dynamicLinks(string $property = 'body'): string
    {
        $text = $this->$property ?? '';
        preg_match_all(
            '/(?:{|%7B)!!(?:\s|%20)([a-z]+):(\d+)(?:\s|%20)!!(?:}|%7D)/',
            (string) $text,
            $matches,
            PREG_SET_ORDER,
        );
        $patterns = [];
        $replacements = [];
        $lang = app()->getLocale();
        foreach ($matches as $match) {
            $patterns[] = $match[0];
            $module = $match[1];
            if (in_array($module, ['page', 'tag', 'user', 'term', 'taxonomy'], true)) {
                $classname = 'TypiCMS\Modules\Core\Models\\' . ucfirst($module);
            } else {
                $classname = 'TypiCMS\Modules\\' . ucfirst(Str::plural($module)) . '\Models\\' . ucfirst($module);
            }

            $model = null;
            if (class_exists($classname)) {
                $model = resolve($classname)->published()->find($match[2]);
            }

            if ($model === null) {
                continue;
            }

            if ($module === 'page') {
                $replacements[] = $model->url($lang) ?? '';
            } elseif (Route::has($lang . '::' . $module)) {
                $replacements[] = route($lang . '::' . $module, $model->slug);
            } else {
                $replacements[] = '';
            }
        }

        return str_replace($patterns, $replacements, $text);
    }
}

<?php

namespace TypiCMS\Modules\Core\Presenters;

use Bkwld\Croppa\Facades\Croppa;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter as BasePresenter;

abstract class Presenter extends BasePresenter
{
    protected $entity;

    protected $imageNotFound = 'img-not-found.png';

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Allow for property-style retrieval.
     *
     * @param mixed $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->{$property}();
        }

        return $this->entity->{$property};
    }

    /**
     * Return a localized date.
     */
    public function dateLocalized(string $column = 'date', string $format = 'LL'): string
    {
        return $this->entity->{$column}->isoFormat($format);
    }

    /**
     * Return a localized date and time.
     */
    public function dateTimeLocalized(string $column = 'datetime', string $format = 'LLL'): string
    {
        return $this->entity->{$column}->isoFormat($format);
    }

    /**
     * Return resource's datetime or current date and time if empty.
     */
    public function datetimeOrNow(string $column = 'date'): string
    {
        $date = $this->entity->{$column} ?: Carbon::now();

        return $date->format('Y-m-d\TH:i');
    }

    /**
     * Return resource's date or current date if empty.
     */
    public function dateOrNow(string $column = 'date'): string
    {
        $date = $this->entity->{$column} ?: Carbon::now();

        return $date->format('Y-m-d');
    }

    /**
     * Return resource's time or current time if empty.
     */
    public function timeOrNow(string $column = 'date'): string
    {
        $date = $this->entity->{$column} ?: Carbon::now();

        return $date->format('H:i');
    }

    protected function getImagePathOrDefault(string $relationName): string
    {
        $path = '';

        if (is_object($this->entity->{$relationName})) {
            $path = $this->entity->{$relationName}->path;
        }

        if (!is_file(Storage::path($path))) {
            $path = $this->imgNotFound();
        }

        return $path;
    }

    /**
     * Return URL of a resized or cropped image.
     */
    public function image(?int $width = null, ?int $height = null, array $options = [], string $relationName = 'image'): string
    {
        $path = $this->getImagePathOrDefault($relationName);

        if (in_array(pathinfo($path, PATHINFO_EXTENSION), ['svg', 'gif'])) {
            return Storage::url($path);
        }

        return url(Croppa::url('storage/' . $path, $width, $height, $options));
    }

    /**
     * Get default image when not found.
     */
    public function imgNotFound(): string
    {
        if (!Storage::exists($this->imageNotFound)) {
            Storage::put($this->imageNotFound, File::get(resource_path('images/' . $this->imageNotFound)));
        }

        return $this->imageNotFound;
    }

    public function ogImage(): string
    {
        if (!empty($this->entity->ogImage)) {
            return $this->image(1200, 630, [], 'ogImage');
        }
        if (!empty($this->entity->image)) {
            return $this->image(1200, 630);
        }

        return Vite::asset(config('typicms.og_image'));
    }

    public function title(): string
    {
        if (!$this->entity->id) {
            return '';
        }

        return strip_tags($this->entity->title);
    }

    /**
     * Return body content with dynamic links.
     */
    public function body(): string
    {
        $text = $this->entity->body;
        preg_match_all('/{!! ([a-z]+):([0-9]+) !!}/', $text, $matches, PREG_SET_ORDER);
        $patterns = [];
        $replacements = [];
        $lang = app()->getLocale();
        if (is_array($matches)) {
            foreach ($matches as $match) {
                $patterns[] = $match[0];
                $module = $match[1];
                if (in_array($module, ['page', 'tag', 'user', 'term', 'taxonomy'])) {
                    $classname = 'TypiCMS\Modules\Core\Models\\' . ucfirst($module);
                } else {
                    $classname = 'TypiCMS\Modules\\' . ucfirst(Str::plural($module)) . '\Models\\' . ucfirst($module);
                }
                $model = null;
                if (class_exists($classname)) {
                    $model = app($classname)
                        ->published()
                        ->find($match[2]);
                }
                if ($model === null) {
                    continue;
                }
                if ($module === 'page') {
                    $replacements[] = $model->url($lang);
                } else {
                    if (Route::has($lang . '::' . $module)) {
                        $replacements[] = route($lang . '::' . $module, $model->slug);
                    } else {
                        $replacements[] = '';
                    }
                }
            }
        }

        return str_replace($patterns, $replacements, $text);
    }
}

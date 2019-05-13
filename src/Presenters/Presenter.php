<?php

namespace TypiCMS\Modules\Core\Presenters;

use Carbon\Carbon;
use Croppa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter as BasePresenter;

abstract class Presenter extends BasePresenter
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Allow for property-style retrieval.
     *
     * @param $property
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
     *
     * @param string $column
     *
     * @return Carbon
     */
    public function dateLocalized($column = 'date')
    {
        return $this->entity->$column->formatLocalized('%e %B %Y');
    }

    /**
     * Return a localized date and time.
     *
     * @param string $column
     *
     * @return Carbon
     */
    public function dateTimeLocalized($column = 'datetime')
    {
        return $this->entity->$column->formatLocalized('%e %B %Y %H:%M');
    }

    /**
     * Return resource's datetime or curent date and time if empty.
     *
     * @param string $column
     *
     * @return Carbon
     */
    public function datetimeOrNow($column = 'date')
    {
        $date = $this->entity->$column ?: Carbon::now();

        return $date->format('Y-m-d\TH:i');
    }

    /**
     * Return resource's date or curent date if empty.
     *
     * @param string $column
     *
     * @return Carbon
     */
    public function dateOrNow($column = 'date')
    {
        $date = $this->entity->$column ?: Carbon::now();

        return $date->format('Y-m-d');
    }

    /**
     * Return resource's time or curent time if empty.
     *
     * @param string $column
     *
     * @return Carbon
     */
    public function timeOrNow($column = 'date')
    {
        $date = $this->entity->$column ?: Carbon::now();

        return $date->format('H:i');
    }

    /**
     * Get url without http(s)://.
     *
     * @param string $column
     *
     * @return string
     */
    public function urlWithoutScheme($column = 'website')
    {
        return str_replace(['http://', 'https://'], '', $this->entity->$column);
    }

    /**
     * Generate an external link.
     *
     * @param string $column
     *
     * @return string
     */
    public function link($column = 'website')
    {
        return '<a href="'.$this->entity->$column.'" target="_blank" rel="noopener noreferrer">'.$this->urlWithoutScheme($column).'</a>';
    }

    /**
     * Get the path of the first image linked to this model
     * or the path to the default image.
     *
     * @return string path
     */
    protected function getImagePathOrDefault()
    {
        $path = '';

        if (is_object($this->entity->image)) {
            $path = $this->entity->image->path;
        }

        if (!Storage::exists($path)) {
            $path = $this->imgNotFound();
        }

        return $path;
    }

    /**
     * Return src string of a resized or cropped image.
     *
     * @param int   $width   width of image, null for auto
     * @param int   $height  height of image, null for auto
     * @param array $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     *
     * @return string
     */
    public function image($width = null, $height = null, array $options = [])
    {
        $url = 'storage/'.$this->getImagePathOrDefault();

        if (pathinfo($url, PATHINFO_EXTENSION) === 'svg') {
            return Storage::url($url);
        }

        return url(Croppa::url($url, $width, $height, $options));
    }

    /**
     * Get default image when not found.
     *
     * @return string
     */
    public function imgNotFound()
    {
        $file = 'img-not-found.png';
        if (!Storage::exists($file)) {
            Storage::put($file, File::get(public_path('img/'.$file)));
        }

        return $file;
    }

    /**
     * Return body content with dynamic links.
     *
     * @return string
     */
    public function body()
    {
        $text = $this->entity->body;
        preg_match_all('/{!! ([a-z]+):([0-9]+) !!}/', $text, $matches, PREG_SET_ORDER);
        $patterns = [];
        $replacements = [];
        $lang = config('app.locale');
        if (is_array($matches)) {
            foreach ($matches as $match) {
                $patterns[] = $match[0];
                $module = $match[1];
                $repository = app('TypiCMS\Modules\\'.ucfirst(Str::plural($module)).'\Repositories\Eloquent'.ucfirst($module));
                $model = $repository->published()->find($match[2]);
                if (!$model) {
                    continue;
                }
                if ($module == 'page') {
                    $replacements[] = url($model->uri($lang));
                } else {
                    if (Route::has($lang.'::'.$module)) {
                        $replacements[] = route($lang.'::'.$module, $model->slug);
                    } else {
                        $replacements[] = '';
                    }
                }
            }
        }

        return str_replace($patterns, $replacements, $text);
    }
}

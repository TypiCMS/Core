<?php

namespace TypiCMS\Modules\Core\Presenters;

use Carbon\Carbon;
use Croppa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

        return $date->format('Y-m-d G:i:s');
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

        return $date->format('G:i');
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
    protected function getImageUrlOrDefault()
    {
        $file = '';

        if (is_object($this->entity->image)) {
            $file = $this->entity->image->path;
        }

        if (!Storage::exists($file)) {
            $file = $this->imgNotFound();
        }

        return Storage::url($file);
    }

    /**
     * Return src string of a resized or cropped image.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     *
     * @return string
     */
    public function thumbSrc($width = null, $height = null, array $options = [])
    {
        $url = $this->getImageUrlOrDefault();

        if (pathinfo($url, PATHINFO_EXTENSION) === 'svg') {
            return $url;
        }

        return Croppa::url($url, $width, $height, $options);
    }

    /**
     * Return url of a thumb.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     *
     * @return string HTML markup of an image
     */
    public function thumbUrl($width = null, $height = null, array $options = [])
    {
        $src = $this->thumbSrc($width, $height, $options);

        return url($src);
    }

    /**
     * Return a resized or cropped img tag.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     *
     * @return string HTML img tag
     */
    public function thumb($width = null, $height = null, array $options = [])
    {
        $src = $this->thumbSrc($width, $height, $options);

        return $this->img($src);
    }

    /**
     * Alias of thumb method.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     *
     * @deprecated
     *
     * @return string HTML img tag
     */
    public function thumb2x($width = null, $height = null, array $options = [])
    {
        return $this->thumb($width, $height, $options);
    }

    /**
     * Build image tag.
     *
     * @param string $src
     * @param int    $width
     * @param int    $height
     *
     * @return string HTML img tag
     */
    private function img($src, $width = null, $height = null)
    {
        $size = [];
        if ($width !== null) {
            $size[] = 'width="'.$width.'"';
        }
        if ($height !== null) {
            $size[] = 'height="'.$height.'"';
        }

        return '<img src="'.$src.'" alt="" '.implode(' ', $size).'>';
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
                $repository = app('TypiCMS\Modules\\'.ucfirst(str_plural($module)).'\Repositories\Eloquent'.ucfirst($module));
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

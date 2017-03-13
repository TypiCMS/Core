<?php

namespace TypiCMS\Modules\Core\Presenters;

use Carbon\Carbon;
use Croppa;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
        return $this->entity->$column->formatLocalized('%d %B %Y');
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
        return $this->entity->$column->formatLocalized('%d %B %Y %H:%M');
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
     * Get url without http://.
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
        return '<a href="'.$this->entity->$column.'" target="_blank">'.$this->urlWithoutScheme($column).'</a>';
    }

    /**
     * Get the path of files linked to this model.
     *
     * @param Model  $model
     * @param string $field
     *
     * @return string path
     */
    protected function getPath(Model $model, $field = null)
    {
        $path = '';
        try {
            $path = '/uploads/files/'.$model->$field->name;
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return $path;
    }

    /**
     * Return src string of a resized or cropped image.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     * @param string $field   column name
     *
     * @return string HTML markup of an image
     */
    public function thumbSrc($width = null, $height = null, array $options = [], $field = 'image')
    {
        $src = $this->getPath($this->entity, $field);
        if (!is_file(public_path().$src)) {
            $src = $this->imgNotFound();
        }

        return Croppa::url($src, $width, $height, $options);
    }

    /**
     * Return url of a thumb.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     * @param string $field   column name
     *
     * @return string HTML markup of an image
     */
    public function thumbUrl($width = null, $height = null, array $options = [], $field = 'image')
    {
        $src = $this->thumbSrc($width, $height, $options, $field);

        return url($src);
    }

    /**
     * Return a resized or cropped img tag.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     * @param string $field   column name
     *
     * @return string HTML img tag
     */
    public function thumb($width = null, $height = null, array $options = [], $field = 'image')
    {
        $src = $this->thumbSrc($width, $height, $options, $field);

        return $this->img($src);
    }

    /**
     * Alias of thumb method.
     *
     * @param int    $width   width of image, null for auto
     * @param int    $height  height of image, null for auto
     * @param array  $options see Croppa doc for options (https://github.com/BKWLD/croppa)
     * @param string $field   column name
     *
     * @deprecated
     *
     * @return string HTML img tag
     */
    public function thumb2x($width = null, $height = null, array $options = [], $field = 'image')
    {
        return $this->thumb($width, $height, $options, $field);
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
     * @param string $file
     *
     * @return string
     */
    public function imgNotFound($file = '/uploads/img-not-found.png')
    {
        return $file;
    }

    /**
     * Return an icon and file name.
     *
     * @param int    $size  icon size
     * @param string $field column name
     *
     * @return string
     */
    public function icon($size = 2, $field = 'document')
    {
        $file = $this->getPath($this->entity, $field);
        $html = '<div class="doc">';
        $html .= '<span class="doc-icon fa fa-file-text-o fa-'.$size.'x"></span>';
        $html .= ' <a class="doc-anchor" href="'.$file.'">';
        $html .= $this->entity->$field;
        $html .= '</a>';
        if (!is_file(public_path().$file)) {
            $html .= ' <span class="doc-warning text-warning">('.__('Not found').')</span>';
        }
        $html .= '</div>';

        return $html;
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
                    $replacements[] = route($lang.'::'.$module, $model->slug);
                }
            }
        }

        return str_replace($patterns, $replacements, $text);
    }
}

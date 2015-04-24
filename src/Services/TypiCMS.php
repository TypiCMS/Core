<?php
namespace TypiCMS\Services;

use App;
use HTML;
use Route;
use Config;
use Request;
use Url;

class TypiCMS
{
    private $model;

    /**
     * Set model
     *
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get Homepage URI
     *
     * @return string
     */
    public function homepage()
    {
        $uri = '/';
        if (config('typicms.main_locale_in_url') || Config::get('app.fallback_locale') != App::getLocale()) {
            $uri .= App::getLocale();
        }
        return $uri;
    }

    /**
     * Return online public locales
     *
     * @return array
     */
    public function getPublicLocales()
    {
        $locales = Config::get('translatable.locales');
        foreach ($locales as $key => $locale) {
            if (! Config::get('typicms.' . $locale . '.status')) {
                unset($locales[$key]);
            }
        }
        return $locales;
    }

    /**
     * Build languages menu
     *
     * @param array $attributes
     * @return string
     */
    public function languagesMenu(array $attributes = array())
    {
        $locales = $this->getPublicLocales();
        $langsArray = $this->buildLangsArray($locales);
        return HTML::languagesMenu($langsArray, $attributes);
    }

    /**
     * Build langs array
     *
     * @param array $locales
     * @return array
     */
    private function buildLangsArray(array $locales = array())
    {
        $langsArray = array();
        foreach ($locales as $locale) {
            $langsArray[] = (object) array(
                'lang' => $locale,
                'url' => $this->getTranslatedUrl($locale),
                'class' => Config::get('app.locale') == $locale ? 'active' : ''
            );
        }
        return $langsArray;
    }

    /**
     * Get url from model
     *
     * @param string $lang
     * @return string
     */
    private function getTranslatedUrl($lang)
    {
        if ($this->model && $this->model->id) {
            return $this->model->getPublicUri(false, false, $lang);
        }
        if ($routeName = Route::current()->getUri() != '/') {
            $routeName = $lang . strstr(Route::current()->getName(), '.');
            if ($routeName == $lang) {
                return '/' . $lang;
            }
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }
        return '/' . $lang;
    }

    /**
     * Get public url when no model loaded
     *
     * @return string
     */
    public function getPublicUrl()
    {
        $lang = Config::get('app.locale');
        $routeArray = explode('.', Route::current()->getName());
        $routeArray[0] = $lang;
        array_pop($routeArray);
        $route = implode('.', $routeArray);

        if (Route::has($route)) {
            return route($route);
        }
        if (
            ! Config::get('typicms.lang_chooser') &&
            Config::get('app.fallback_locale') == $lang &&
            ! config('typicms.main_locale_in_url')
        ) {
            return '/';
        }
        return '/' . $lang;
    }

    /**
     * Build public link
     *
     * @param array $attributes
     * @return string
     */
    public function publicLink(array $attributes = array())
    {
        $url = $this->getPublicUrl();
        $title = ucfirst(trans('global.view website', array(), null, Config::get('typicms.admin_locale')));
        return HTML::link($url, $title, $attributes);
    }

    /**
     * Build admin link
     *
     * @param array $attributes
     * @return string
     */
    public function adminLink(array $attributes = array())
    {
        $url = route('dashboard');
        $title = ucfirst(trans('global.admin side', array(), null, Config::get('typicms.admin_locale')));
        if ($this->model) {
            if (! $this->model->id) {
                $url = $this->model->indexUrl();
            } else {
                $url = $this->model->editUrl();
            }
            $url .= '?locale=' . App::getLocale();
        }
        return HTML::link($url, $title, $attributes);
    }

    /**
     * Build admin or public link
     *
     * @param array $attributes
     * @return string
     */
    public function otherSideLink(array $attributes = array())
    {
        if ($this->isAdmin()) {
            return $this->publicLink($attributes);
        }
        return $this->adminLink($attributes);
    }

    /**
     * Check if we are on back office
     *
     * @return boolean true if we are on backend
     */
    public function isAdmin()
    {
        if (Request::segment(1) == 'admin') {
            return true;
        }
        return false;
    }

    /**
     * Get all modules for permissions table.
     *
     * @return array
     */
    public function modules()
    {
        $modules = config('typicms.modules');
        ksort($modules);
        return $modules;
    }

    /**
     * Get all modules for a select/options
     *
     * @return array
     */
    public function getModulesForSelect()
    {
        $modules = config('typicms.modules');
        $options = ['' => ''];
        foreach ($modules as $module => $properties) {
            if (in_array('linkable_to_page', $properties)) {
                $options[$module] = trans($module . '::global.name');
            }
        }
        asort($options);
        return $options;
    }

    /**
     * Get logo from settings
     *
     * @return string
     */
    public function logo()
    {
        if (config('typicms.image')) {
            return '<img src="' . Url::to('uploads/settings/' . config('typicms.image')) . '" alt="' . $this->title() . '">';
        }
        return null;
    }

    /**
     * Get title from settings
     *
     * @return string
     */
    public function title()
    {
        return config('typicms.' . config('app.locale') . '.website_title');
    }

    /**
     * Get title from settings
     *
     * @return string
     */
    public function logoOrTitle()
    {
        return $this->logo() ? : $this->title();
    }

}

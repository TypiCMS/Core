<?php

namespace TypiCMS\Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class TypiCMS
{
    /**
     * Get Homepage URL.
     *
     * @return string
     */
    public function homeUrl()
    {
        $uri = '/';
        if (config('typicms.main_locale_in_url') || config('app.fallback_locale') != config('app.locale')) {
            $uri .= config('app.locale');
        }

        return url($uri);
    }

    /**
     * Return enabled public locales.
     *
     * @return array
     */
    public function enabledLocales()
    {
        $locales = [];
        foreach (locales() as $locale) {
            if (config('typicms.'.$locale.'.status')) {
                $locales[] = $locale;
            }
        }

        return $locales;
    }

    /**
     * Check if locale is online.
     *
     * @return bool
     */
    public function isLocaleEnabled($locale)
    {
        return in_array($locale, $this->enabledLocales());
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
     * Get all modules for a select/options.
     *
     * @return array
     */
    public function getModulesForSelect()
    {
        $modules = config('typicms.modules');
        $options = ['' => ''];
        foreach ($modules as $module => $properties) {
            if (in_array('linkable_to_page', $properties)) {
                $options[$module] = trans($module.'::global.name');
            }
        }
        asort($options);

        return $options;
    }

    /**
     * Check if there is a logo.
     *
     * @return bool
     */
    public function hasLogo()
    {
        return (bool) config('typicms.image');
    }

    /**
     * Get title from settings.
     *
     * @return string
     */
    public function title()
    {
        return config('typicms.'.config('app.locale').'.website_title');
    }

    /**
     * Return the first page found linked to a module.
     *
     * @param string $module
     *
     * @return \TypiCMS\Modules\Pages\Models\Page
     */
    public function getPageLinkedToModule($module = null)
    {
        $pages = $this->getPagesLinkedToModule($module);

        return reset($pages);
    }

    /**
     * Return an array of pages linked to a module.
     *
     * @param string $module
     *
     * @return array
     */
    public function getPagesLinkedToModule($module = null)
    {
        $module = strtolower($module);
        $routes = app('typicms.routes');

        $pages = [];
        foreach ($routes as $page) {
            if ($page->module == $module) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    /**
     * List templates files from directory.
     *
     * @return array
     */
    public function templates()
    {
        try {
            $directory = $this->getTemplateDir();
            $files = File::files($directory);
        } catch (Exception $e) {
            $files = File::files(base_path('vendor/typicms/pages/src/resources/views/public'));
        }
        $templates = [];
        foreach ($files as $file) {
            $filename = File::name($file);
            $name = str_replace('.blade', '', $filename);
            if ($name[0] != '_' && $name != 'master') {
                $templates[$name] = ucfirst($name);
            }
        }

        return ['' => ''] + $templates;
    }

    public function getTemplateDir()
    {
        $templateDir = config('typicms.template_dir', 'public');
        $viewPath = app()['view']->getFinder()->getHints()['pages'][0];

        return rtrim($viewPath.DIRECTORY_SEPARATOR.$templateDir, DIRECTORY_SEPARATOR);
    }

    public function feeds()
    {
        $locale = config('app.locale');
        $feeds = collect(config('typicms.modules'))
            ->transform(function ($properties, $module) use ($locale) {
                $routeName = $locale.'.'.$module.'.feed';
                if (in_array('has_feed', $properties) && Route::has($routeName)) {
                    return ['url' => route($routeName), 'title' => trans($module.'::global.feed').' – '.$this->title()];
                }
            })->reject(function ($value) {
                return empty($value);
            });

        return $feeds;
    }
}

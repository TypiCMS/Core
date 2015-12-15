<?php

namespace TypiCMS\Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\File;

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
     * Get Homepage URL.
     *
     * @deprecated
     *
     * @return string
     */
    public function homepage()
    {
        return $this->homeUrl();
    }

    /**
     * Return online public locales.
     *
     * @return array
     */
    public function getOnlineLocales()
    {
        $locales = config('translatable.locales');
        foreach ($locales as $key => $locale) {
            if (!config('typicms.'.$locale.'.status')) {
                unset($locales[$key]);
            }
        }

        return $locales;
    }

    /**
     * Check if locale is online.
     *
     * @return bool
     */
    public function isLocaleOnline($locale)
    {
        return in_array($locale, $this->getOnlineLocales());
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
     * Get logo from settings.
     *
     * @return string
     */
    public function logo()
    {
        if (config('typicms.image')) {
            return '<img src="'.url('uploads/settings/'.config('typicms.image')).'" alt="'.$this->title().'">';
        }

        return;
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
     * Get title from settings.
     *
     * @return string
     */
    public function logoOrTitle()
    {
        return $this->logo() ?: $this->title();
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
    public function templates($directory = 'resources/views/vendor/pages/public')
    {
        $templates = [];
        try {
            $files = File::allFiles(base_path($directory));
        } catch (Exception $e) {
            $files = File::allFiles(base_path('vendor/typicms/pages/src/resources/views/public'));
        }
        foreach ($files as $key => $file) {
            $name = str_replace('.blade.php', '', $file->getRelativePathname());
            if ($name[0] != '_' && $name != 'master') {
                $templates[$name] = ucfirst($name);
            }
        }

        return ['' => ''] + $templates;
    }
}

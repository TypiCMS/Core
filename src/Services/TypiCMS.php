<?php
namespace TypiCMS\Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\File;

class TypiCMS
{

    /**
     * Get Homepage URI
     *
     * @return string
     */
    public function homepage()
    {
        $uri = '/';
        if (config('typicms.main_locale_in_url') || config('app.fallback_locale') != config('app.locale')) {
            $uri .= config('app.locale');
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
        $locales = config('translatable.locales');
        foreach ($locales as $key => $locale) {
            if (! config('typicms.' . $locale . '.status')) {
                unset($locales[$key]);
            }
        }
        return $locales;
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
            return '<img src="' . url('uploads/settings/' . config('typicms.image')) . '" alt="' . $this->title() . '">';
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

    /**
     * Return an array of pages linked to modules
     *
     * @param  string $module
     * @return array|Model
     */
    public function routes()
    {
        return app('TypiCMS.routes');
    }

    /**
     * Return an array of pages linked to modules
     *
     * @param  string $module
     * @return array|Model
     */
    public function getPageLinkedToModule($module = null)
    {
        $module = strtolower($module);
        $routes = $this->routes();
        if (isset($routes[$module])) {
            return $routes[$module];
        }
        return null;
    }

    /**
     * List templates files from directory
     * @return array
     */
    public function template($directory = 'resources/views/vendor/pages/public')
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

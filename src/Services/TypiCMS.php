<?php

namespace TypiCMS\Modules\Core\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use TypiCMS\Modules\Core\Models\Page;

class TypiCMS
{
    public function homeUrl(): string
    {
        $uri = '/';
        if (config('typicms.main_locale_in_url') || $this->mainLocale() !== config('app.locale')) {
            $uri .= config('app.locale');
        }

        return url($uri);
    }

    public function enabledLocales(): array
    {
        $locales = [];
        foreach (locales() as $locale) {
            if (config('typicms.'.$locale.'.status') || request('preview')) {
                $locales[] = $locale;
            }
        }

        return $locales;
    }

    public function mainLocale(): string
    {
        return Arr::first(locales());
    }

    public function localeAndRegion(string $separator = null, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $locales = config('typicms.locales');
        if (!array_key_exists($locale, $locales)) {
            return null;
        }
        $localeAndRegion = $locales[$locale];
        if (!is_null($separator)) {
            return str_replace('_', $separator, $localeAndRegion);
        }

        return $localeAndRegion;
    }

    public function isLocaleEnabled($locale): bool
    {
        return in_array($locale, $this->enabledLocales());
    }

    public function getBrowserLocaleOrMainLocale(): string
    {
        if ($locale = mb_substr(getenv('HTTP_ACCEPT_LANGUAGE'), 0, 2)) {
            if (in_array($locale, $this->enabledLocales())) {
                return $locale;
            }
        }

        return $this->mainLocale();
    }

    public function modules(): array
    {
        $modules = config('typicms.modules');
        ksort($modules);

        return $modules;
    }

    public function getModulesForSelect(): array
    {
        $modules = config('typicms.modules');
        $options = ['' => ''];
        foreach ($modules as $module => $properties) {
            if (isset($properties['linkable_to_page']) && $properties['linkable_to_page'] === true) {
                $options[$module] = __(ucfirst($module));
            }
        }
        asort($options);

        return $options;
    }

    public function permissions(): array
    {
        $permissions = [];
        foreach (config('typicms.modules') as $module => $data) {
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $key = __(ucfirst($module));
                $permissions[$key] = $data['permissions'];
            }
        }
        ksort($permissions, SORT_LOCALE_STRING);

        return $permissions;
    }

    public function hasLogo(): bool
    {
        return (bool) config('typicms.image');
    }

    public function title($locale = null): ?string
    {
        return config('typicms.'.($locale ?: config('app.locale')).'.website_title');
    }

    public function baseline($locale = null): ?string
    {
        return config('typicms.'.($locale ?: config('app.locale')).'.website_baseline');
    }

    public function getPageLinkedToModule($module = null): ?Page
    {
        $pages = $this->getPagesLinkedToModule($module);

        return Arr::first($pages);
    }

    public function getPagesLinkedToModule($module = null): array
    {
        $module = mb_strtolower($module);
        $routes = app('typicms.routes');

        $pages = [];
        foreach ($routes as $page) {
            if ($page->module === $module) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    public function pageTemplates(): array
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
            if ($filename === 'default.blade') {
                continue;
            }
            $name = str_replace('.blade', '', $filename);
            if ($name[0] != '_' && $name != 'master') {
                $templates[$name] = ucfirst($name);
            }
        }

        return ['' => 'Default'] + $templates;
    }

    public function pageSectionTemplates(): array
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
            if ($filename === '_section-default.blade') {
                continue;
            }
            if (str_starts_with($filename, '_section-')) {
                $name = str_replace(['_section-', '.blade'], '', $filename);
                $templates[$name] = ucfirst($name);
            }
        }

        return ['default' => 'Default'] + $templates;
    }

    public function getTemplateDir(): string
    {
        $templateDir = config('typicms.template_dir', 'public');
        $viewPath = app()['view']->getFinder()->getHints()['pages'][0];

        return rtrim($viewPath.DIRECTORY_SEPARATOR.$templateDir, DIRECTORY_SEPARATOR);
    }

    public function feeds(): Collection
    {
        $locale = config('app.locale');
        $feeds = collect(config('typicms.modules'))
            ->transform(function ($properties, $module) use ($locale) {
                $routeName = $locale.'::'.$module.'-feed';
                if (isset($properties['has_feed']) && $properties['has_feed'] === true && Route::has($routeName)) {
                    return ['url' => route($routeName, $module), 'title' => __(ucfirst($module).' feed').' – '.$this->title()];
                }
            })->reject(function ($value) {
                return empty($value);
            });

        return $feeds;
    }
}

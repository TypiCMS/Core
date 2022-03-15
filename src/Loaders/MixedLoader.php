<?php

namespace TypiCMS\Modules\Core\Loaders;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\FileLoader;
use TypiCMS\Modules\Core\Models\Translation;

class MixedLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string      $locale
     * @param string      $group
     * @param string|null $namespace
     */
    public function load($locale, $group, $namespace = null): array
    {
        if ($group === '*' && $namespace === '*') {
            $jsonTranslations = $this->loadJsonPaths($locale);
            $databaseTranslations = $this->loadFromDatabase($locale, $group, $namespace);

            return array_replace_recursive($jsonTranslations, $databaseTranslations);
        }

        if (is_null($namespace) || $namespace === '*') {
            return $this->loadPath($this->path, $locale, $group);
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }

    /**
     * Load the messages from the database.
     */
    public function loadFromDatabase(string $locale, string $group, string $namespace = null): array
    {
        try {
            return Translation::select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(`translation`, '$.".$locale."')) AS translated"), 'key')
                ->pluck('translated', 'key')
                ->all();
        } catch (Exception $e) {
        }

        return [];
    }
}

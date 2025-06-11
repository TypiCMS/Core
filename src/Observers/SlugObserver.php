<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Support\Str;

class SlugObserver
{
    public function saving(mixed $model): void
    {
        $titles = $model->getTranslations('title');
        $slugs = $model->getTranslations('slug');

        foreach ($titles as $locale => $title) {
            $slug = $slugs[$locale] ?? Str::slug($title);

            if ($slug) {
                $i = 0;
                // Check slug is unique
                while ($this->slugExists($model, $locale)) {
                    $i++;
                    // increment slug if exists
                    $model->setTranslation('slug', $locale, $slug . '-' . $i);
                }
            }
        }
    }

    /**
     * Search for item with same slug.
     */
    private function slugExists(mixed $model, string $locale): bool
    {
        $query = $model::query()->where('slug->' . $locale, $model->getTranslation('slug', $locale));
        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return (bool) $query->count();
    }
}

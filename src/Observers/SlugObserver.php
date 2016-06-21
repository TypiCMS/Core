<?php

namespace TypiCMS\Modules\Core\Observers;

class SlugObserver
{
    public function saving($model)
    {
        $titles = $model->getTranslations('title');
        $slugs = $model->getTranslations('slug');

        foreach ($titles as $locale => $title) {
            $slug = $model->slugs[$locale] ?: str_slug($title);
            // slug = null if empty string
            $model->setTranslation('slug', $locale, $slug ?: null);

            if ($slug) {
                $i = 0;
                // Check slug is unique
                while ($this->slugExists($model, $locale)) {
                    $i++;
                    // increment slug if exists
                    $model->setTranslation('slug', $locale, $slug.'-'.$i);
                }
            }
        }
    }

    /**
     * Search for item with same slug.
     *
     * @param mixed $model
     *
     * @return bool
     */
    private function slugExists($model, $locale)
    {
        $query = $model::where('slug->'.$locale, $model->getTranslation('slug', $locale));
        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return (bool) $query->count();
    }
}

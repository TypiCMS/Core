<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Support\Str;

class SlugObserver
{
    public function saving(mixed $model): void
    {
        $slugs = $model->getTranslations('slug');

        foreach ($model->getTranslations('title') as $locale => $title) {
            $baseSlug = $slugs[$locale] ?? Str::slug($title);

            if ($baseSlug === '') {
                continue;
            }

            $count = 1;
            while ($this->slugExists($model, $locale)) {
                $model->setTranslation('slug', $locale, sprintf('%s-%d', $baseSlug, $count));
                $count++;
            }
        }
    }

    private function slugExists(mixed $model, string $locale): bool
    {
        return $model::query()
            ->where('slug->' . $locale, $model->getTranslation('slug', $locale))
            ->when($model->id, fn ($query) => $query->where('id', '!=', $model->id))
            ->exists();
    }
}

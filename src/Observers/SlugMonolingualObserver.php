<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Support\Str;

class SlugMonolingualObserver
{
    public function saving(mixed $model): void
    {
        if ($model->slug === null) {
            $generatedSlug = Str::slug($model->title);
            $model->slug = $generatedSlug !== '' ? $generatedSlug : null;
        }

        if ($model->slug === null) {
            return;
        }

        $baseSlug = $model->slug;
        $counter = 0;

        while ($this->slugExists($model)) {
            $counter++;
            $model->slug = "{$baseSlug}-{$counter}";
        }
    }

    private function slugExists(mixed $model): bool
    {
        return $model::query()
            ->where('slug', $model->slug)
            ->when($model->id, fn ($query) => $query->where('id', '!=', $model->id))
            ->exists();
    }
}

<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Support\Str;

class SlugMonolingualObserver
{
    public function saving(mixed $model): void
    {
        $slug = $model->slug ?: Str::slug($model->title);
        $model->slug = $slug;

        if ($slug) {
            $i = 0;
            // Check slug is unique
            while ($this->slugExists($model)) {
                $i++;
                // increment slug if exists
                $model->slug = $slug . '-' . $i;
            }
        }
    }

    private function slugExists(mixed $model): bool
    {
        $query = $model::query()->where('slug', $model->slug);
        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return (bool) $query->count();
    }
}

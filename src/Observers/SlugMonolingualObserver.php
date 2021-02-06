<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugMonolingualObserver
{
    public function saving(Model $model)
    {
        $slug = $model->slug ?: Str::slug($model->title);
        $model->slug = $slug;

        if ($slug) {
            $i = 0;
            // Check slug is unique
            while ($this->slugExists($model)) {
                ++$i;
                // increment slug if exists
                $model->slug = $slug.'-'.$i;
            }
        }
    }

    private function slugExists(Model $model): bool
    {
        $query = $model::where('slug', $model->slug);
        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return (bool) $query->count();
    }
}

<?php

namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;

class SlugObserverMonolingual
{
    public function saving(Model $model)
    {
        $slug = $model->slug ?: str_slug($model->title);

        if ($slug) {
            $i = 0;
            // Check slug is unique
            while ($this->slugExists($model)) {
                $i++;
                // increment slug if exists
                $model->slug = $slug.'-'.$i;
            }
        }
    }

    private function slugExists(Model $model)
    {
        $query = $model::where('slug', $model->slug);
        if ($model->id) {
            $query->where('id', '!=', $model->id);
        }

        return (bool) $query->count();
    }
}

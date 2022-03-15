<?php

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Models\History;

trait Historable
{
    /**
     * boot method.
     */
    public static function bootHistorable()
    {
        static::created(function (Model $model) {
            $model->writeHistory('created', Str::limit($model->present()->title, 200, '…'), [], $model->toArray());
        });

        static::updated(function (Model $model) {
            $action = 'updated';

            $new = [];
            $old = [];
            foreach ($model->attributes as $key => $value) {
                if ($model->translatable and in_array($key, $model->translatable)) {
                    $values = (array) json_decode($value);
                    $originalValues = (array) json_decode($model->original[$key]);
                    foreach ($values as $locale => $newItem) {
                        if (isset($originalValues[$locale]) && $newItem !== $originalValues[$locale]) {
                            $new[$key][$locale] = $newItem;
                            $old[$key][$locale] = $originalValues[$locale];
                        }
                    }
                } else {
                    $originalValue = $model->original[$key];
                    if ($value != $originalValue) {
                        $new[$key] = $value;
                        $old[$key] = $originalValue;
                    }
                }
            }

            $model->writeHistory($action, Str::limit($model->present()->title, 200, '…'), $old, $new);
        });

        static::deleted(function (Model $model) {
            $model->writeHistory('deleted', Str::limit($model->present()->title, 200, '…'));
        });
    }

    /**
     * Write History row.
     */
    public function writeHistory(string $action, ?string $title = null, array $old = [], array $new = [])
    {
        History::create([
            'historable_id' => $this->getKey(),
            'historable_type' => get_class($this),
            'user_id' => auth()->id(),
            'title' => $title,
            'historable_table' => $this->getTable(),
            'action' => $action,
            'old' => $old,
            'new' => $new,
        ]);
    }

    /**
     * Model has history.
     */
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'historable');
    }
}

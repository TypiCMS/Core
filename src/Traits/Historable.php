<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Models\History;

trait Historable
{
    public static function bootHistorable(): void
    {
        static::created(function (mixed $model): void {
            $model->writeHistory('created', Str::limit($model->present()->title, 200, '…'), [], $model->toArray());
        });

        static::updated(function (mixed $model): void {
            $action = 'updated';

            $new = [];
            $old = [];
            foreach ($model->attributes as $key => $value) {
                if ($model->translatable && in_array($key, $model->translatable, true)) {
                    $values = (array) json_decode($value);
                    $originalValues = (array) json_decode((string) $model->original[$key]);
                    foreach ($values as $locale => $newItem) {
                        if (isset($originalValues[$locale]) && $newItem !== $originalValues[$locale]) {
                            $new[$key][$locale] = $newItem;
                            $old[$key][$locale] = $originalValues[$locale];
                        }
                    }
                } else {
                    $originalValue = $model->original[$key] ?? '';
                    if ($value !== $originalValue) {
                        $new[$key] = $value;
                        $old[$key] = $originalValue;
                    }
                }
            }

            $model->writeHistory($action, Str::limit($model->present()->title, 200, '…'), $old, $new);
        });

        static::deleted(function (mixed $model): void {
            $model->writeHistory('deleted', Str::limit($model->present()->title, 200, '…'));
        });
    }

    /**
     * Write History row.
     *
     * @param array<string, mixed> $old
     * @param array<string, mixed> $new
     */
    public function writeHistory(string $action, ?string $title = null, array $old = [], array $new = []): void
    {
        History::query()->create([
            'historable_id' => $this->getKey(),
            'historable_type' => $this::class,
            'user_id' => auth()->id(),
            'title' => $title,
            'historable_table' => $this->getTable(),
            'action' => $action,
            'old' => $old,
            'new' => $new,
        ]);
    }

    /** @return MorphMany<History, $this> */
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'historable');
    }
}

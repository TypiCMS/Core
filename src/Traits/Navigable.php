<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;

trait Navigable
{
    public function next(mixed $model, ?int $category_id = null): ?Model
    {
        return $this->adjacent(1, $model, $category_id);
    }

    public function prev(mixed $model, ?int $category_id = null): ?Model
    {
        return $this->adjacent(-1, $model, $category_id);
    }

    public function adjacent(int $direction, mixed $model, ?int $category_id = null): ?Model
    {
        $currentModel = $model;
        if ($category_id !== null) {
            $models = self::query()
                ->published()
                ->with('category')
                ->order()
                ->where('category_id', $category_id)
                ->get(['id', 'category_id', 'slug', 'title']);
        } else {
            $models = self::query()
                ->published()
                ->order()
                ->get(['id', 'slug', 'title']);
        }

        foreach ($models as $key => $model) {
            if ($currentModel->id === $model->id) {
                $adjacentKey = $key + $direction;

                return $models[$adjacentKey] ?? null;
            }
        }

        return null;
    }
}

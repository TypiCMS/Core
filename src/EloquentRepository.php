<?php

namespace TypiCMS\Modules\Core;

use Carbon\Carbon;
use Rinvex\Repository\Repositories\EloquentRepository as BaseRepository;

class EloquentRepository extends BaseRepository
{
    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = [])
    {
        return $this->with($with);
    }

    /**
     * Get next model.
     *
     * @param Model $model
     * @param int   $category_id
     * @param array $with
     * @param bool  $all
     *
     * @return Model|null
     */
    public function next($model, $category_id = null, array $with = [], $all = false)
    {
        return $this->adjacent(1, $model, $category_id, $with, $all);
    }

    /**
     * Get prev model.
     *
     * @param Model $model
     * @param int   $category_id
     * @param array $with
     * @param bool  $all
     *
     * @return Model|null
     */
    public function prev($model, $category_id = null, array $with = [], $all = false)
    {
        return $this->adjacent(-1, $model, $category_id, $with, $all);
    }

    /**
     * Get prev model.
     *
     * @param int   $direction
     * @param Model $model
     * @param int   $category_id
     * @param array $with
     * @param bool  $all
     *
     * @return Model|null
     */
    public function adjacent($direction, $model, $category_id = null, array $with = [], $all = false)
    {
        $currentModel = $model;
        if ($category_id !== null) {
            $models = $this->with(['category'])->findWhere(['category_id', $category_id], ['id', 'slug']);
        } else {
            $models = $this->published()->findAll(['id', 'slug']);
        }
        foreach ($models as $key => $model) {
            if ($currentModel->id == $model->id) {
                $adjacentKey = $key + $direction;

                return isset($models[$adjacentKey]) ? $models[$adjacentKey] : null;
            }
        }
    }

    /**
     * Get single model by Slug.
     *
     * @param string $slug
     * @param array  $attributes
     *
     * @return mixed
     */
    public function bySlug($slug, $attributes = ['*'])
    {
        $model = $this
            ->findBy(column('slug'), $slug, $attributes);

        if (is_null($model)) {
            abort(404);
        }

        return $model;
    }

    public function published()
    {
        return $this->where(column('status'), '1');
    }

    /**
     * Get all models sorted, filtered and paginated.
     *
     * @param array $columns
     * @param array $with
     *
     * @return array
     */
    public function allFiltered($columns = [], array $with = [])
    {
        $columns = $columns ?: ['*'];
        $params = request()->all();
        $data = $this->with($with)->select($columns);

        $orderBy = $params['orderBy'] ?? null;
        $query = $params['query'] ?? null;
        $limit = $params['limit'] ?? null;
        $page = $params['page'] ?? 1;
        $ascending = $params['ascending'] ?? 1;
        $byColumn = $params['byColumn'] ?? 0;

        if ($query !== null) {
            $data = $byColumn == 1 ? $this->filterByColumn($data, $query) :
                                     $this->filter($data, $query, $columns);
        }

        $count = $data->count();

        if ($limit !== null) {
            $data->limit($limit)->skip($limit * ($page - 1));
        }
        if ($orderBy !== null) {
            $orderBy .= $this->translatableOperator($orderBy);
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }
        $results = $data->get()
            ->translate(config('typicms.content_locale'));

        return [
            'data'  => $results,
            'count' => $count,
        ];
    }

    private function filterByColumn($data, $query)
    {
        foreach ($query as $column => $query) {
            if (!$query) {
                continue;
            }
            $column .= $this->translatableOperator($column);
            if (is_string($query)) {
                $data->where($column, 'LIKE', "%{$query}%");
            } else {
                $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();
                $data->whereBetween($column, [$start, $end]);
            }
        }

        return $data;
    }

    private function filter($data, $query, $columns)
    {
        foreach ($columns as $index => $column) {
            $column .= $this->translatableOperator($column);
            $method = $index ? 'orWhere' : 'where';
            $data->{$method}($column, 'LIKE', "%{$query}%");
        }

        return $data;
    }

    /**
     * Add ->$locale to column name.
     *
     * @param string $column
     *
     * @return string|null
     */
    private function translatableOperator($column)
    {
        if (in_array($column, $this->createModel()->translatable)) {
            return '->'.config('typicms.content_locale');
        }
    }

    /**
     * Get all models and nest.
     *
     * @param bool  $all  Show published or all
     * @param array $with Eager load related models
     *
     * @return NestedCollection
     */
    public function allNested(array $with = [], $all = false)
    {
        return $this->all($with, $all)->translate(config('typicms.content_locale'))->nest();
    }
}

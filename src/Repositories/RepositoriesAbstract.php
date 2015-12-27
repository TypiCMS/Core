<?php

namespace TypiCMS\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use stdClass;
use TypiCMS\Modules\Pages\Models\Page;
use TypiCMS\NestedCollection;

abstract class RepositoriesAbstract implements RepositoryInterface
{
    protected $model;

    /**
     * Get empty model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = [])
    {
        if (method_exists($this->model, 'translations')) {
            if (!in_array('translations', $with)) {
                $with[] = 'translations';
            }
        }

        return $this->model->with($with);
    }

    /**
     * Find a single entity by key value.
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     */
    public function getFirstBy($key, $value, array $with = [], $all = false)
    {
        $query = $this->make($with);
        if (!$all) {
            $query->online();
        }

        return $query->where($key, '=', $value)->first();
    }

    /**
     * Retrieve model by id
     * regardless of status.
     *
     * @param int $id model ID
     *
     * @return Model
     */
    public function byId($id, array $with = [])
    {
        $query = $this->make($with)->where('id', $id);

        $model = $query->firstOrFail();

        return $model;
    }

    /**
     * Get next model.
     *
     * @param Model $model
     * @param int   $category_id
     * @param array $with
     * @param bool  $all
     *
     * @return Collection
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
     * @return Collection
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
     * @return Collection
     */
    public function adjacent($direction, $model, $category_id = null, array $with = [], $all = false)
    {
        $currentModel = $model;
        if ($category_id) {
            $with[] = 'category';
            $with[] = 'category.translations';
            $models = $this->allBy('category_id', $category_id, $with, $all);
        } else {
            $models = $this->all($with, $all);
        }
        foreach ($models as $key => $model) {
            if ($currentModel->id == $model->id) {
                $adjacentKey = $key + $direction;

                return isset($models[$adjacentKey]) ? $models[$adjacentKey] : null;
            }
        }

        return;
    }

    /**
     * Get paginated models.
     *
     * @param int   $page  Number of models per page
     * @param int   $limit Results per page
     * @param bool  $all   get published models or all
     * @param array $with  Eager load related models
     *
     * @return stdClass Object with $items && $totalItems for pagination
     */
    public function byPage($page = 1, $limit = 10, array $with = [], $all = false)
    {
        $result = new stdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = [];

        $query = $this->make($with);

        if (!$all) {
            $query->online();
        }

        $totalItems = $query->count();

        $query->order()
            ->skip($limit * ($page - 1))
            ->take($limit);

        $models = $query->get();

        // Put items and totalItems in stdClass
        $result->totalItems = $totalItems;
        $result->items = $models->all();

        return $result;
    }

    /**
     * Get all models.
     *
     * @param array $with Eager load related models
     * @param bool  $all  Show published or all
     *
     * @return Collection|NestedCollection
     */
    public function all(array $with = [], $all = false)
    {
        $query = $this->make($with);

        if (!$all) {
            $query->online();
        }

        // Query ORDER BY
        $query->order();

        // Get
        return $query->get();
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
        // Get
        return $this->all($with, $all)->nest();
    }

    /**
     * Get all models by key/value.
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     * @param bool   $all
     *
     * @return Collection
     */
    public function allBy($key, $value, array $with = [], $all = false)
    {
        $query = $this->make($with);

        if (!$all) {
            $query->online();
        }

        $query->where($key, $value);

        // Query ORDER BY
        $query->order();

        // Get
        $models = $query->get();

        return $models;
    }

    /**
     * Get all models by key/value and nest collection.
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     * @param bool   $all
     *
     * @return Collection
     */
    public function allNestedBy($key, $value, array $with = [], $all = false)
    {
        // Get
        return $this->allBy($key, $value, $with, $all)->nest();
    }

    /**
     * Get latest models.
     *
     * @param int   $number number of items to take
     * @param array $with   array of related items
     *
     * @return Collection
     */
    public function latest($number = 10, array $with = [])
    {
        $query = $this->make($with);

        return $query->online()->order()->take($number)->get();
    }

    /**
     * Get single model by Slug.
     *
     * @param string $slug slug
     * @param array  $with related tables
     *
     * @return mixed
     */
    public function bySlug($slug, array $with = [])
    {
        $model = $this->make($with)
            ->whereHas(
                'translations',
                function (Builder $query) use ($slug) {
                    if (!Input::get('preview')) {
                        $query->where('status', 1);
                    }
                    $query->where('locale', config('app.locale'));
                    $query->where('slug', '=', $slug);
                }
            )
            ->firstOrFail();

        if (!count($model->translations)) {
            abort(404);
        }

        return $model;
    }

    /**
     * Return all results that have a required relationship.
     *
     * @param string $relation
     * @param array  $with
     *
     * @return Collection
     */
    public function has($relation, array $with = [])
    {
        $entity = $this->make($with);

        return $entity->has($relation)->get();
    }

    /**
     * Create a new model.
     *
     * @param array $data
     *
     * @return mixed Model or false on error during save
     */
    public function create(array $data)
    {
        // Create the model
        $model = $this->model->fill($data);

        if ($model->save()) {
            $this->syncRelation($model, $data, 'galleries');

            return $model;
        }

        return false;
    }

    /**
     * Update an existing model.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $model = $this->model->find($data['id']);

        $model->fill($data);

        $this->syncRelation($model, $data, 'galleries');

        if ($model->save()) {
            return true;
        }

        return false;
    }

    /**
     * Sort models.
     *
     * @param array $data updated data
     *
     * @return void
     */
    public function sort(array $data)
    {
        foreach ($data['item'] as $position => $item) {
            $page = $this->model->find($item['id']);

            $sortData = $this->getSortData($position + 1, $item);

            $page->update($sortData);

            if ($data['moved'] == $item['id']) {
                $this->fireResetChildrenUriEvent($page);
            }
        }
    }

    /**
     * Get sort data.
     *
     * @param int   $position
     * @param array $item
     *
     * @return array
     */
    protected function getSortData($position, $item)
    {
        return [
            'position' => $position,
        ];
    }

    /**
     * Fire event to reset children’s uri
     * Only applicable on nestable collections.
     *
     * @param Page $page
     *
     * @return void|null
     */
    protected function fireResetChildrenUriEvent($page)
    {
        return;
    }

    /**
     * Get all translated pages for a select/options.
     *
     * @return array
     */
    public function getPagesForSelect()
    {
        $pages = app('TypiCMS\Modules\Pages\Repositories\PageInterface')
            ->all([], true)
            ->nest()
            ->listsFlattened();
        $pages = ['' => ' '] + $pages;

        return $pages;
    }

    /**
     * Delete model.
     *
     * @return bool
     */
    public function delete($model)
    {
        if ($model->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Sync related items for model.
     *
     * @param Model  $model
     * @param array  $data
     * @param string $table
     */
    public function syncRelation($model, array $data, $table = null)
    {
        if (!method_exists($model, $table)) {
            return false;
        }

        if (!isset($data[$table])) {
            return false;
        }

        // add related items
        $pivotData = [];
        $position = 0;
        if (is_array($data[$table])) {
            foreach ($data[$table] as $id) {
                $pivotData[$id] = ['position' => $position++];
            }
        }

        // Sync related items
        $model->$table()->sync($pivotData);
    }
}

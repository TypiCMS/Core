<?php

namespace TypiCMS\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use TypiCMS\NestedCollection;

abstract class CacheAbstractDecorator implements RepositoryInterface
{
    protected $repo;
    protected $cache;

    /**
     * Get empty model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->repo->getModel();
    }

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->repo->getTable();
    }

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = [])
    {
        return $this->repo->make($with);
    }

    /**
     * Retrieve model by id
     * regardless of status.
     *
     * @param int $id model ID
     *
     * @return stdObject object of model information
     */
    public function byId($id, array $with = [])
    {
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'id.'.serialize($with).$id.serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $model = $this->repo->byId($id, $with);

        $this->cache->put($cacheKey, $model);

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
     * @return Model|null
     */
    public function next($model, $category_id = null, array $with = [], $all = false)
    {
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'next.'.$model->id.$all.$category_id.serialize($with));
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        // Item not cached, retrieve it
        $next = $this->repo->next($model, $category_id, $with, $all);

        $this->cache->put($cacheKey, $next);

        return $next;
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
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'prev.'.$model->id.$all.$category_id.serialize($with));
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        // Item not cached, retrieve it
        $prev = $this->repo->prev($model, $category_id, $with, $all);

        $this->cache->put($cacheKey, $prev);

        return $prev;
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
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'adjacent.'.$direction.$model->id.$category_id.serialize($with).$all);
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        // Item not cached, retrieve it
        $model = $this->repo->adjacent($direction, $model, $category_id, $with, $all);

        $this->cache->put($cacheKey, $model);

        return $model;
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
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'getFirstBy'.$key.$value.serialize($with).$all.serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $model = $this->repo->getFirstBy($key, $value, $with, $all);

        $this->cache->put($cacheKey, $model);

        return $model;
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
        $cacheKey = md5(config('app.locale').'byPage'.$page.$limit.serialize($with).$all.serialize(Request::except('page')));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $models = $this->repo->byPage($page, $limit, $with, $all);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

        return $models;
    }

    /**
     * Get all models.
     *
     * @param bool  $all  Show published or all
     * @param array $with Eager load related models
     *
     * @return Collection
     */
    public function all(array $with = [], $all = false)
    {
        $cacheKey = md5(config('app.locale').'all'.serialize($with).$all.serialize(Request::except('page')));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $models = $this->repo->all($with, $all);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

        return $models;
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
        $cacheKey = md5(config('app.locale').'allNested'.serialize($with).$all.serialize(Request::except('page')));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $models = $this->repo->allNested($with, $all);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

        return $models;
    }

    /**
     * Get all models by key/value.
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     * @param bool   $all
     *
     * @return stdClass Object with $items
     */
    public function allBy($key, $value, array $with = [], $all = false)
    {
        $cacheKey = md5(config('app.locale').'allBy'.$key.$value.serialize($with).$all.serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $models = $this->repo->allBy($key, $value, $with, $all);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

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
     * @return stdClass Object with $items
     */
    public function allNestedBy($key, $value, array $with = [], $all = false)
    {
        $cacheKey = md5(config('app.locale').'allNestedBy'.$key.$value.serialize($with).$all.serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $models = $this->repo->allNestedBy($key, $value, $with, $all);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

        return $models;
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
        $cacheKey = md5(config('app.locale').'latest'.$number.serialize($with).serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $models = $this->repo->latest($number, $with);

        // Store in cache for next request
        $this->cache->put($cacheKey, $models);

        return $models;
    }

    /**
     * Get single model by slug.
     *
     * @param string $slug of model
     * @param array  $with
     *
     * @return object object of model information
     */
    public function bySlug($slug, array $with = [])
    {
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'bySlug'.$slug.serialize($with).serialize(Request::all()));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $model = $this->repo->bySlug($slug, $with);

        // Store in cache for next request
        $this->cache->put($cacheKey, $model);

        return $model;
    }

    /**
     * Return all results that have a required relationship.
     *
     * @param string $relation
     * @param array  $with
     */
    public function has($relation, array $with = [])
    {
        // Build the cache key, unique per model slug
        $cacheKey = md5(config('app.locale').'has'.serialize($with).$relation);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        // Item not cached, retrieve it
        $model = $this->repo->has($relation, $with);

        // Store in cache for next request
        $this->cache->put($cacheKey, $model);

        return $model;
    }

    /**
     * Create a new model.
     *
     * @param array  Data needed for model creation
     *
     * @return mixed Model or false on error during save
     */
    public function create(array $data)
    {
        $this->cache->flush();
        $this->cache->flush('dashboard');

        return $this->repo->create($data);
    }

    /**
     * Update an existing model.
     *
     * @param array  Data needed for model update
     *
     * @return bool
     */
    public function update(array $data)
    {
        $this->cache->flush();

        return $this->repo->update($data);
    }

    /**
     * Sort models.
     *
     * @param array  Data to update Pages
     *
     * @return bool|null
     */
    public function sort(array $data)
    {
        $this->cache->flush();
        $this->repo->sort($data);
    }

    /**
     * Delete model.
     *
     * @return bool
     */
    public function delete($model)
    {
        $this->cache->flush();
        $this->cache->flush('dashboard');

        return $this->repo->delete($model);
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
        return $this->repo->syncRelation($model, $data, $table);
    }
}

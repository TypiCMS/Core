<?php

namespace TypiCMS\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use stdClass;
use TypiCMS\NestedCollection;

interface RepositoryInterface
{
    /**
     * Get empty model.
     *
     * @return Model
     */
    public function getModel();

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = []);

    /**
     * Find a single entity by key value.
     *
     * @param string $key
     * @param string $value
     * @param array  $with
     */
    public function getFirstBy($key, $value, array $with = [], $all = false);

    /**
     * Retrieve model by id
     * regardless of status.
     *
     * @param int $id model ID
     *
     * @return Model
     */
    public function byId($id, array $with = []);

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
    public function next($model, $category_id = null, array $with = [], $all = false);

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
    public function prev($model, $category_id = null, array $with = [], $all = false);

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
    public function adjacent($direction, $model, $category_id = null, array $with = [], $all = false);

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
    public function byPage($page = 1, $limit = 10, array $with = [], $all = false);

    /**
     * Get all models.
     *
     * @param array $with Eager load related models
     * @param bool  $all  Show published or all
     *
     * @return Collection|NestedCollection
     */
    public function all(array $with = [], $all = false);

    /**
     * Get all models and nest.
     *
     * @param bool  $all  Show published or all
     * @param array $with Eager load related models
     *
     * @return NestedCollection
     */
    public function allNested(array $with = [], $all = false);

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
    public function allBy($key, $value, array $with = [], $all = false);

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
    public function allNestedBy($key, $value, array $with = [], $all = false);

    /**
     * Get latest models.
     *
     * @param int   $number number of items to take
     * @param array $with   array of related items
     *
     * @return Collection
     */
    public function latest($number = 10, array $with = []);

    /**
     * Get single model by Slug.
     *
     * @param string $slug slug
     * @param array  $with related tables
     *
     * @return mixed
     */
    public function bySlug($slug, array $with = []);

    /**
     * Return all results that have a required relationship.
     *
     * @param string $relation
     * @param array  $with
     *
     * @return Collection
     */
    public function has($relation, array $with = []);

    /**
     * Create a new model.
     *
     * @param  array  Data needed for model creation
     *
     * @return mixed Model or false on error during save
     */
    public function create(array $data);

    /**
     * Update an existing model.
     *
     * @param  array  Data needed for model update
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Sort models.
     *
     * @param  array  Data to update Pages
     *
     * @return bool
     */
    public function sort(array $data);

    /**
     * Delete model.
     *
     * @return bool
     */
    public function delete($model);

    /**
     * Sync related items for model.
     *
     * @param Model  $model
     * @param array  $data
     * @param string $table
     */
    public function syncRelation($model, array $data, $table = null);
}

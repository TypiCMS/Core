<?php

namespace TypiCMS\Modules\Core\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Rinvex\Repository\Repositories\EloquentRepository as BaseRepository;
use TypiCMS\Modules\Files\Models\File;

class EloquentRepository extends BaseRepository
{
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
            $models = $this->with('category')->findWhere(['category_id', $category_id], ['id', 'category_id', 'slug']);
        } else {
            $models = $this->all(['id', 'slug']);
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
        if (!request('preview')) {
            $this->published();
        }
        $model = $this->findBy(column('slug'), $slug, $attributes);

        if (is_null($model)) {
            abort(404);
        }

        return $model;
    }

    /**
     * Get models where status = 1.
     */
    public function published()
    {
        return $this->where(column('status'), '1');
    }

    /**
     * Get latest models.
     *
     * @param int $number number of items to take
     *
     * @return Collection
     */
    public function latest($number = 10)
    {
        if (!request('preview')) {
            $this->published();
        }

        return $this->executeCallback(static::class, __FUNCTION__, func_get_args(), function () use ($number) {
            return $this->prepareQuery($this->createModel())
                ->order()
                ->take($number)
                ->get();
        });
    }

    /**
     * Get all models by key/value.
     *
     * @param string $key
     * @param string $value
     *
     * @return Collection
     */
    public function allBy($key, $value)
    {
        if (!request('preview')) {
            $this->published();
        }

        return $this->executeCallback(static::class, __FUNCTION__, func_get_args(), function () use ($key, $value) {
            return $this->prepareQuery($this->createModel())
                ->where($key, $value)
                ->order()
                ->get();
        });
    }

    /**
     * Get all models.
     *
     * @param string $key
     * @param string $value
     *
     * @return Collection
     */
    public function all()
    {
        if (!request('preview')) {
            $this->published();
        }

        return $this->executeCallback(static::class, __FUNCTION__, func_get_args(), function () {
            return $this->prepareQuery($this->createModel())
                ->order()
                ->get();
        });
    }

    public function paginate($perPage = null, $attributes = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        if (!request('preview')) {
            $this->published();
        }

        return $this->executeCallback(static::class, __FUNCTION__, array_merge(func_get_args(), compact('page')), function () use ($perPage, $attributes, $pageName, $page) {
            return $this->prepareQuery($this->createModel())->order()->paginate($perPage, $attributes, $pageName, $page);
        });
    }

    /**
     * Find all entities translated.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Support\Collection
     */
    public function findAllTranslated($attributes = ['*'])
    {
        $models = $this->findAll($attributes)->translate(config('typicms.content_locale'));

        return $models;
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
            'data' => $results,
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
     * Sort models.
     *
     * @param array $data updated data
     *
     * @return null
     */
    public function sort(array $data)
    {
        foreach ($data['item'] as $position => $item) {
            $page = $this->find($item['id']);

            $sortData = $this->getSortData($position + 1, $item);

            $page->update($sortData);

            if ($data['moved'] == $item['id']) {
                $this->fireResetChildrenUriEvent($page);
            }
        }
        $this->forgetCache();
    }

    /**
     * Remove files from model.
     */
    public function detachFile($page, $file)
    {
        $filesIds = $page->files->pluck('id')->toArray();
        $pivotData = [];
        $position = 1;
        foreach ($filesIds as $fileId) {
            if ($fileId != $file->id) {
                $pivotData[$fileId] = ['position' => $position++];
            }
        }
        $page->files()->sync($pivotData);
        $this->forgetCache();
    }

    /**
     * Attach files to a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Http\Request            $request
     *
     * @return null
     */
    public function attachFiles($model, Request $request)
    {
        // Get the collection of files to add.
        $fileIds = $request->only('files')['files'];
        $files = File::whereIn('id', $fileIds)->get();

        // Empty collection that will contain files that are in selected directories.
        $subFiles = collect();

        // Remove folders and build array of ids.
        $newFiles = $files->each(function (Model $item) use ($subFiles) {
            if ($item->type === 'f') {
                foreach ($item->children as $file) {
                    if ($file->type !== 'f') {
                        // Add files in this directory to collection of SubFiles
                        $subFiles->push($file);
                    }
                }
            }
        })->reject(function (Model $item) {
            return $item->type === 'f';
        })
            ->pluck('id')
            ->toArray();

        // Transform subfiles collection to array of ids.
        $subFiles = $subFiles->pluck('id')->toArray();

        // Merge files with files in directory.
        $newFiles = array_merge($newFiles, $subFiles);

        // Get files that are already in the gallery.
        $filesIds = $model->files->pluck('id')->toArray();

        // Merge with new files.
        $files = array_unique(array_merge($filesIds, $newFiles));
        $number = count($files) - count($filesIds);

        // Prepare synchronisation.
        $pivotData = [];
        $position = 1;
        foreach ($files as $fileId) {
            $pivotData[$fileId] = ['position' => $position++];
        }

        // Sync.
        $model->files()->sync($pivotData);

        $this->forgetCache();

        return response()->json([
            'number' => $number,
            'message' => __(':number items were added.', compact('number')),
            'models' => $model->files()->get(),
        ]);
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
     * Fire event to reset children’s uri.
     * Only applicable on nestable collections.
     *
     * @param Page $page
     *
     * @return null
     */
    protected function fireResetChildrenUriEvent($page)
    {
    }
}

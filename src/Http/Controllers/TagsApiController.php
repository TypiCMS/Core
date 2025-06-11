<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Tag;

class TagsApiController extends BaseApiController
{
    /** @return LengthAwarePaginator<int, mixed> */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Tag::query()->selectFields();
        $data = QueryBuilder::for($query)
            ->allowedSorts(['tag', 'uses'])
            ->allowedFilters([
                AllowedFilter::custom('tag', new FilterOr()),
            ])
            ->addSelect([
                'uses' => DB::table('taggables')->selectRaw('COUNT(*)')->whereColumn('tags.id', 'taggables.tag_id'),
            ])
            ->paginate($request->integer('per_page'));

        return $data;
    }

    /** @return Collection<int, Model> */
    public function tagsList(): Collection
    {
        return QueryBuilder::for(Tag::class)->get();
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(status: 204);
    }
}

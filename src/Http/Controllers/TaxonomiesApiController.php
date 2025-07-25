<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Taxonomy;

class TaxonomiesApiController extends BaseApiController
{
    /** @return LengthAwarePaginator<int, mixed> */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Taxonomy::query()->selectFields();
        $data = QueryBuilder::for($query)
            ->allowedSorts(['title_translated', 'validation_rule', 'result_string_translated', 'position', 'name'])
            ->allowedFilters([
                AllowedFilter::custom('title,name,validation_rule,result_string', new FilterOr()),
            ])
            ->paginate($request->integer('per_page'));

        return $data;
    }

    protected function updatePartial(Taxonomy $taxonomy, Request $request): void
    {
        foreach ($request->only('position') as $key => $content) {
            if ($taxonomy->isTranslatableAttribute($key)) {
                foreach ($content as $lang => $value) {
                    $taxonomy->setTranslation($key, $lang, $value);
                }
            } else {
                $taxonomy->{$key} = $content;
            }
        }

        $taxonomy->save();
    }

    public function destroy(Taxonomy $taxonomy): JsonResponse
    {
        if ($taxonomy->terms->count() > 0) {
            return response()->json(['message' => __('This taxonomy cannot be deleted as it contains terms.')], 403);
        }
        $taxonomy->delete();

        return response()->json(status: 204);
    }
}

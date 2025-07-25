<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Translation;

class TranslationsApiController extends BaseApiController
{
    /** @return LengthAwarePaginator<int, mixed> */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Translation::query()->selectFields();
        $data = QueryBuilder::for($query)
            ->allowedSorts(['key', 'translation_translated'])
            ->allowedFilters([
                AllowedFilter::custom('key,translation', new FilterOr()),
            ])
            ->paginate($request->integer('per_page'));

        return $data;
    }

    public function destroy(Translation $translation): JsonResponse
    {
        $translation->delete();

        return response()->json(status: 204);
    }
}

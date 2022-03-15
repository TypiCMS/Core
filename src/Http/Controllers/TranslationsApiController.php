<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Translation;

class TranslationsApiController extends BaseApiController
{
    public function index(Request $request): LengthAwarePaginator
    {
        $data = QueryBuilder::for(Translation::class)
            ->selectFields($request->input('fields.translations'))
            ->allowedSorts(['key', 'translation_translated'])
            ->allowedFilters([
                AllowedFilter::custom('key,translation', new FilterOr()),
            ])
            ->paginate($request->input('per_page'));

        return $data;
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Page;
use TypiCMS\Modules\Core\Models\PageSection;

class PageSectionsApiController extends BaseApiController
{
    public function index(Page $page, Request $request): LengthAwarePaginator
    {
        $query = PageSection::query()->selectFields();
        $data = QueryBuilder::for($query)
            ->allowedSorts(['status_translated', 'position', 'title_translated'])
            ->allowedFilters([
                AllowedFilter::custom('title', new FilterOr()),
            ])
            ->allowedIncludes(['image'])
            ->where('page_id', $page->id)
            ->paginate($request->integer('per_page'));

        return $data;
    }

    protected function updatePartial(Page $page, PageSection $section, Request $request)
    {
        foreach ($request->only('status', 'position') as $key => $content) {
            if ($section->isTranslatableAttribute($key)) {
                foreach ($content as $lang => $value) {
                    $section->setTranslation($key, $lang, $value);
                }
            } else {
                $section->{$key} = $content;
            }
        }

        $section->save();
    }

    public function destroy(Page $page, PageSection $section): JsonResponse
    {
        $section->delete();

        return response()->json(status: 204);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Menu;

class MenusApiController extends BaseApiController
{
    /** @return LengthAwarePaginator<int, mixed> */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Menu::query()->selectFields();

        return QueryBuilder::for($query)
            ->allowedSorts(['status_translated', 'name'])
            ->allowedFilters([
                AllowedFilter::custom('name', new FilterOr()),
            ])
            ->allowedIncludes(['image'])
            ->paginate($request->integer('per_page'));
    }

    protected function updatePartial(Menu $menu, Request $request): void
    {
        foreach ($request->only('status') as $key => $content) {
            if ($menu->isTranslatableAttribute($key)) {
                foreach ($content as $lang => $value) {
                    $menu->setTranslation($key, $lang, $value);
                }
            } else {
                $menu->{$key} = $content;
            }
        }

        $menu->save();
    }

    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return response()->json(status: 204);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Models\Menu;
use TypiCMS\Modules\Core\Models\Menulink;
use TypiCMS\NestableCollection;

class MenulinksApiController extends BaseApiController
{
    public function index(Menu $menu, Request $request): NestableCollection
    {
        $userPreferences = $request->user()->preferences;

        $query = Menulink::query()
            ->selectFields()
            ->where('menu_id', $menu->id)
            ->orderBy('position');
        $data = QueryBuilder::for($query)
            ->get()
            ->map(function (Menulink $menulink) use ($userPreferences) {
                $menulink->data = $menulink->toArray();
                $menulink->isLeaf = false;
                $menulink->isExpanded = !Arr::get($userPreferences, 'Menulinks_' . $menulink->id . '_collapsed', false);

                return $menulink;
            })
            ->childrenName('children')
            ->nest();

        return $data;
    }

    protected function updatePartial(Menu $menu, Menulink $menulink, Request $request): void
    {
        foreach ($request->only('status') as $key => $content) {
            if ($menulink->isTranslatableAttribute($key)) {
                foreach ($content as $lang => $value) {
                    $menulink->setTranslation($key, $lang, $value);
                }
            } else {
                $menulink->{$key} = $content;
            }
        }

        $menulink->save();
    }

    public function sort(Menu $menu, Request $request): void
    {
        $data = $request->only('moved', 'item');
        foreach ($data['item'] as $position => $item) {
            $menulink = Menulink::query()->find($item['id']);
            $sortData = [
                'position' => (int) $position + 1,
                'parent_id' => $item['parent_id'],
            ];
            $menulink->update($sortData);
        }
    }

    public function destroy(Menu $menu, Menulink $menulink): JsonResponse
    {
        if ($menulink->submenulinks->count() > 0) {
            return response()->json(['message' => 'This item cannot be deleted because it has children.'], 403);
        }
        $menulink->delete();

        return response()->json(status: 204);
    }
}

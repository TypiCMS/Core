<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use TypiCMS\Modules\Core\Models\Menu;
use TypiCMS\Modules\Core\Models\Menulink;

class MenulinksApiController extends BaseApiController
{
    /** @return array{models: Collection<int, Model>, total: int} */
    public function index(Menu $menu, Request $request): array
    {
        $userPreferences = $request->user()->preferences;

        $query = Menulink::query()
            ->selectFields()
            ->where('menu_id', $menu->id)
            ->orderBy('position');

        $total = $query->count();

        $models = $query
            ->get()
            ->map(function (Model $menulink) use ($userPreferences) {
                /** @var Menulink $menulink */
                $menulink->data = $menulink->toArray();
                $menulink->isLeaf = false;
                $menulink->isExpanded = !Arr::get($userPreferences, 'Menulinks_' . $menulink->id . '_collapsed', false);

                return $menulink;
            })
            ->childrenName('children')
            ->nest();

        return compact('models', 'total');
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

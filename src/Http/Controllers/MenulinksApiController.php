<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

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

        $data = QueryBuilder::for(Menulink::class)
            ->selectFields($request->input('fields.menulinks'))
            ->where('menu_id', $menu->id)
            ->orderBy('position')
            ->get()
            ->map(function ($item) use ($userPreferences) {
                $item->data = $item->toArray();
                $item->isLeaf = $item->module === null ? false : true;
                $item->isExpanded = !Arr::get($userPreferences, 'Menulinks_'.$item->id.'_collapsed', false);

                return $item;
            })
            ->childrenName('children')
            ->nest();

        return $data;
    }

    protected function updatePartial(Menu $menu, Menulink $menulink, Request $request)
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

    public function sort(Menu $menu, Request $request)
    {
        $data = $request->only('moved', 'item');
        foreach ($data['item'] as $position => $item) {
            $menulink = Menulink::find($item['id']);
            $sortData = [
                'position' => (int) $position + 1,
                'parent_id' => $item['parent_id'],
            ];
            $menulink->update($sortData);
        }
    }

    public function destroy(Menu $menu, Menulink $menulink)
    {
        if ($menulink->submenulinks->count() > 0) {
            return response(['message' => 'This item cannot be deleted because it has children.'], 403);
        }
        $menulink->delete();
    }
}

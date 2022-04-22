<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Models\Page;
use TypiCMS\NestableCollection;

class PagesApiController extends BaseApiController
{
    public function index(Request $request): NestableCollection
    {
        $userPreferences = $request->user()->preferences;

        $data = QueryBuilder::for(Page::class)
            ->selectFields($request->input('fields.pages'))
            ->orderBy('position')
            ->get()
            ->map(function ($item) use ($userPreferences) {
                $item->data = $item->toArray();
                $item->isLeaf = $item->module === null ? false : true;
                $item->isExpanded = !Arr::get($userPreferences, 'Pages_'.$item->id.'_collapsed', false);

                return $item;
            })
            ->childrenName('children')
            ->nest();

        return $data;
    }

    public function linksForEditor(Request $request)
    {
        $data = Page::order()
            ->get()
            ->nest()
            ->listsFlattened();

        $pages = [];
        foreach ($data as $id => $title) {
            $pages[] = [$title, "{!! page:{$id} !!}"];
        }

        return $pages;
    }

    protected function updatePartial(Page $page, Request $request)
    {
        foreach ($request->only('status') as $key => $content) {
            if ($page->isTranslatableAttribute($key)) {
                foreach ($content as $lang => $value) {
                    $page->setTranslation($key, $lang, $value);
                }
            } else {
                $page->{$key} = $content;
            }
        }

        $page->save();
    }

    public function sort(Request $request)
    {
        $data = $request->only('moved', 'item');
        foreach ($data['item'] as $position => $item) {
            $page = Page::find($item['id']);

            $sortData = [
                'position' => (int) $position + 1,
                'parent_id' => $item['parent_id'],
                'private' => $item['private'],
            ];

            $page->update($sortData);
        }
    }

    public function destroy(Page $page)
    {
        if ($page->isHome()) {
            return response(['message' => 'The home page cannot be deleted.'], 403);
        }
        if ($page->subpages->count() > 0) {
            return response(['message' => 'This item cannot be deleted because it has children.'], 403);
        }
        $page->delete();
    }
}

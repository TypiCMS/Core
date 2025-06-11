<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use TypiCMS\Modules\Core\Models\Page;
use TypiCMS\NestableCollection;

class PagesApiController extends BaseApiController
{
    public function index(Request $request): NestableCollection
    {
        $userPreferences = $request->user()->preferences;

        $query = Page::query()
            ->selectFields()
            ->orderBy('position');

        $pages = Page::query()
            ->selectFields()
            ->orderBy('position')
            ->get()
            ->map(function (Page $page) use ($userPreferences) {
                $page->data = $page->toArray();
                $page->isLeaf = $page->module === null ? false : true;
                $page->isExpanded = !Arr::get($userPreferences, 'Pages_' . $page->id . '_collapsed', false);

                return $page;
            })
            ->childrenName('children')
            ->nest();

        return $pages;
    }

    /** @return list<array<int, mixed>> */
    public function linksForEditor(Request $request): array
    {
        $data = Page::query()
            ->select(['id', 'parent_id', 'title'])
            ->order()
            ->get()
            ->nest()
            ->listsFlattened();

        $pages = [];
        foreach ($data as $id => $title) {
            $pages[] = [$title, "{!! page:{$id} !!}"];
        }

        return $pages;
    }

    protected function updatePartial(Page $page, Request $request): void
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

    public function sort(Request $request): void
    {
        $data = $request->only('moved', 'item');
        foreach ($data['item'] as $position => $item) {
            $page = Page::query()->find($item['id']);

            $sortData = [
                'position' => (int) $position + 1,
                'parent_id' => $item['parent_id'],
                'private' => $item['private'],
            ];

            $page->update($sortData);
        }
    }

    public function destroy(Page $page): JsonResponse
    {
        if ($page->isHome()) {
            return response()->json(['message' => 'The home page cannot be deleted.'], 403);
        }
        if ($page->subpages->count() > 0) {
            return response()->json(['message' => 'This item cannot be deleted because it has children.'], 403);
        }
        $page->delete();

        return response()->json(status: 204);
    }
}

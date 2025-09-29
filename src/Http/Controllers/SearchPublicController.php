<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SearchPublicController extends BasePublicController
{
    public function search(Request $request): View
    {
        $results = collect();
        $tabs = [];
        $count = 0;
        $data = [];
        $data['query'] = e($request->string('query'));
        $validator = Validator::make($data, [
            'query' => 'required|string|min:3',
        ]);
        if ($validator->fails()) {
            return view('search::public.index')
                ->with(['results' => $results, 'count' => $count])
                ->withErrors($validator);
        }

        $config = config('typicms.search');
        $words = array_filter(explode(' ', $data['query']));

        foreach ($config as $key => $data) {
            if (!is_array($data)) {
                continue;
            }
            $model = app($data['model']);
            $columns = $data['columns'];
            $query = $model
                ->query()
                ->where(function ($query) use ($words, $columns, $model, $key): void {
                    foreach ($columns as $column) {
                        $query->orWhere(function ($query) use ($words, $column, $model): void {
                            foreach ($words as $word) {
                                $word = addslashes($word);
                                if (in_array($column, (array) $model->translatable)) {
                                    $query->published()->whereRaw(
                                        'JSON_UNQUOTE(JSON_EXTRACT(`' . $column . '`, \'$.' . app()->getLocale() . '\')) LIKE \'%' . $word . '%\' COLLATE utf8mb4_unicode_ci'
                                    );
                                } else {
                                    $query->published()->whereRaw(
                                        '`' . $column . '` LIKE \'%' . $word . '%\' COLLATE utf8mb4_unicode_ci'
                                    );
                                }
                            }
                        });
                        if ($key === 'pages') { // search in page sections
                            $query->orWhere(function ($query) use ($words, $column, $model): void {
                                $query->published();
                                $query->whereHas('sections', function ($query) use ($words, $column, $model): void {
                                    foreach ($words as $word) {
                                        $word = addslashes($word);
                                        if (in_array($column, (array) $model->translatable)) {
                                            $query->published()->whereRaw(
                                                'JSON_UNQUOTE(JSON_EXTRACT(`' . $column . '`, \'$.' . app()->getLocale() . '\')) LIKE \'%' . $word . '%\' COLLATE utf8mb4_unicode_ci'
                                            );
                                        } else {
                                            $query->published()->whereRaw(
                                                '`' . $column . '` LIKE \'%' . $word . '%\' COLLATE utf8mb4_unicode_ci'
                                            );
                                        }
                                    }
                                });
                            });
                        }
                    }
                });
            $items = $query->order()->get();
            $numberOfItems = $items->count();
            if ($numberOfItems) {
                $tabs[] = ['module' => $key, 'count' => $numberOfItems];
                $results[] = ['module' => $key, 'models' => $items];
                $count += $numberOfItems;
            }
        }

        return view('search::public.index')
            ->with(['results' => $results, 'count' => $count, 'tabs' => $tabs])
            ->withErrors($validator);
    }
}

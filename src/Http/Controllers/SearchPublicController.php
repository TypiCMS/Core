<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchPublicController extends BasePublicController
{
    public function search(Request $request)
    {
        $results = collect();
        $tabs = [];
        $count = 0;
        $data = [];
        $data['search'] = e($request->input('search'));
        $validator = Validator::make($data, [
            'search' => 'required|string|min:3',
        ]);
        if ($validator->fails()) {
            return view('search::public.index')
                ->with(compact('results', 'count'))
                ->withErrors($validator);
        }

        $config = config('typicms.search');
        $words = array_filter(explode(' ', $data['search']));

        foreach ($config as $key => $data) {
            $model = app($data['model']);
            $columns = $data['columns'];
            $query = $model->where(function (Builder $query) use ($words, $columns, $model, $key) {
                foreach ($columns as $column) {
                    $query->orWhere(function ($query) use ($words, $column, $model) {
                        foreach ($words as $word) {
                            $word = addslashes($word);
                            if (in_array($column, (array) $model->translatable)) {
                                $query->published()->whereRaw(
                                    'JSON_UNQUOTE(JSON_EXTRACT(`'.$column.'`, \'$.'.app()->getLocale().'\')) LIKE \'%'.$word.'%\' COLLATE utf8mb4_unicode_ci'
                                );
                            } else {
                                $query->published()->whereRaw(
                                    '`'.$column.'` LIKE \'%'.$word.'%\' COLLATE utf8mb4_unicode_ci'
                                );
                            }
                        }
                    });
                    if ($key === 'pages') { // search in page sections
                        $query->orWhere(function ($query) use ($words, $column, $model) {
                            $query->published();
                            $query->whereHas('sections', function (Builder $query) use ($words, $column, $model) {
                                foreach ($words as $word) {
                                    $word = addslashes($word);
                                    if (in_array($column, (array) $model->translatable)) {
                                        $query->published()->whereRaw(
                                            'JSON_UNQUOTE(JSON_EXTRACT(`'.$column.'`, \'$.'.app()->getLocale().'\')) LIKE \'%'.$word.'%\' COLLATE utf8mb4_unicode_ci'
                                        );
                                    } else {
                                        $query->published()->whereRaw(
                                            '`'.$column.'` LIKE \'%'.$word.'%\' COLLATE utf8mb4_unicode_ci'
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
            ->with(compact('results', 'count', 'tabs'))
            ->withErrors($validator);
    }
}

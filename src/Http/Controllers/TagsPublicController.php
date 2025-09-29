<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\View\View;
use TypiCMS\Modules\Core\Models\Tag;

class TagsPublicController extends BasePublicController
{
    public function index(): View
    {
        $perPage = config('typicms.modules.tags.per_page');
        $models = Tag::query()->paginate($perPage);

        return view('tags::public.index')
            ->with(['models' => $models]);
    }

    public function show(string $slug): View
    {
        $model = Tag::query()->where('slug', $slug)->firstOrFail();

        return view('tags::public.show')
            ->with(['model' => $model]);
    }
}

<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\TaxonomyFormRequest;
use TypiCMS\Modules\Core\Models\Taxonomy;
use TypiCMS\Modules\Core\Models\Term;

class TaxonomiesAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('taxonomies::admin.index');
    }

    public function create(): View
    {
        $model = new Taxonomy();
        $modules = array_filter(config('typicms.modules'), function ($item) {
            return isset($item['has_taxonomies']) && $item['has_taxonomies'] === true;
        });

        return view('taxonomies::admin.create')
            ->with(compact('model', 'modules'));
    }

    public function edit(Taxonomy $taxonomy): View
    {
        $modules = array_filter(config('typicms.modules'), function ($item) {
            return isset($item['has_taxonomies']) && $item['has_taxonomies'] === true;
        });

        return view('taxonomies::admin.edit')
            ->with(['model' => $taxonomy, 'modules' => $modules]);
    }

    public function store(TaxonomyFormRequest $request): RedirectResponse
    {
        $taxonomy = Taxonomy::create($request->validated());

        return $this->redirect($request, $taxonomy)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Taxonomy $taxonomy, TaxonomyFormRequest $request): RedirectResponse
    {
        $taxonomy->update($request->validated());
        (new Term())->flushCache();

        return $this->redirect($request, $taxonomy)
            ->withMessage(__('Item successfully updated.'));
    }
}

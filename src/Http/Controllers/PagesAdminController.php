<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\PageFormRequest;
use TypiCMS\Modules\Core\Models\Page;

class PagesAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('pages::admin.index');
    }

    public function create(): View
    {
        $model = new Page();

        return view('pages::admin.create')
            ->with(compact('model'));
    }

    public function edit(Page $page): View
    {
        return view('pages::admin.edit')
            ->with(['model' => $page]);
    }

    public function store(PageFormRequest $request): RedirectResponse
    {
        $page = Page::create($request->validated());

        return $this->redirect($request, $page)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Page $page, PageFormRequest $request): RedirectResponse
    {
        $page->update($request->validated());

        return $this->redirect($request, $page)
            ->withMessage(__('Item successfully updated.'));
    }

    public function notFound()
    {
        abort(404);
    }
}

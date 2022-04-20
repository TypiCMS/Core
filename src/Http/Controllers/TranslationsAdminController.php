<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\TranslationFormRequest;
use TypiCMS\Modules\Core\Models\Translation;

class TranslationsAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('translations::admin.index');
    }

    public function create(): View
    {
        $model = new Translation();

        return view('translations::admin.create')
            ->with(compact('model'));
    }

    public function edit(Translation $translation): View
    {
        return view('translations::admin.edit')
            ->with(['model' => $translation]);
    }

    public function store(TranslationFormRequest $request): RedirectResponse
    {
        $translation = Translation::create($request->validated());

        return $this->redirect($request, $translation)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Translation $translation, TranslationFormRequest $request): RedirectResponse
    {
        $translation->update($request->validated());

        return $this->redirect($request, $translation)
            ->withMessage(__('Item successfully updated.'));
    }
}

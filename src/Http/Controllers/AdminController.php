<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\FormRequest;
use TypiCMS\Modules\Core\Models\Block;

class AdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('blocks::admin.index');
    }

    public function create(): View
    {
        $model = new Block();

        return view('blocks::admin.create')
            ->with(compact('model'));
    }

    public function edit(Block $block): View
    {
        return view('blocks::admin.edit')
            ->with(['model' => $block]);
    }

    public function store(FormRequest $request): RedirectResponse
    {
        $block = Block::create($request->validated());

        return $this->redirect($request, $block)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Block $block, FormRequest $request): RedirectResponse
    {
        $block->update($request->validated());

        return $this->redirect($request, $block)
            ->withMessage(__('Item successfully updated.'));
    }
}

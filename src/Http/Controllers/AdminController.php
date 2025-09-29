<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\BlockFormRequest;
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
            ->with(['model' => $model]);
    }

    public function edit(Block $block): View
    {
        return view('blocks::admin.edit')
            ->with(['model' => $block]);
    }

    public function store(BlockFormRequest $request): RedirectResponse
    {
        $block = Block::query()->create($request->validated());

        return $this->redirect($request, $block)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Block $block, BlockFormRequest $request): RedirectResponse
    {
        $block->update($request->validated());

        return $this->redirect($request, $block)
            ->withMessage(__('Item successfully updated.'));
    }
}

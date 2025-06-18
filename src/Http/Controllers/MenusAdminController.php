<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\MenuFormRequest;
use TypiCMS\Modules\Core\Models\Menu;

class MenusAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('menus::admin.index');
    }

    public function create(): View
    {
        $model = new Menu();

        return view('menus::admin.create')
            ->with(compact('model'));
    }

    public function edit(Menu $menu): View
    {
        return view('menus::admin.edit')
            ->with(['model' => $menu]);
    }

    public function store(MenuFormRequest $request): RedirectResponse
    {
        $menu = Menu::create($request->validated());

        return $this->redirect($request, $menu)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Menu $menu, MenuFormRequest $request): RedirectResponse
    {
        $menu->update($request->validated());

        return $this->redirect($request, $menu)
            ->withMessage(__('Item successfully updated.'));
    }
}

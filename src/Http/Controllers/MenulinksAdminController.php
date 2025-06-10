<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\MenulinkFormRequest;
use TypiCMS\Modules\Core\Models\Menu;
use TypiCMS\Modules\Core\Models\Menulink;

class MenulinksAdminController extends BaseAdminController
{
    public function create(Menu $menu): View
    {
        $model = new Menulink();

        return view('menus::admin.create-menulink')
            ->with(compact('model', 'menu'));
    }

    public function edit(Menu $menu, Menulink $menulink): View
    {
        return view('menus::admin.edit-menulink')
            ->with([
                'menu' => $menu,
                'model' => $menulink,
            ]);
    }

    public function store(Menu $menu, MenulinkFormRequest $request): RedirectResponse
    {
        $menulink = Menulink::create($request->validated());

        return $this->redirect($request, $menulink)
            ->withMessage(__('Item successfully created.'));
    }

    public function update(Menu $menu, Menulink $menulink, MenulinkFormRequest $request): RedirectResponse
    {
        $menulink->update($request->validated());

        return $this->redirect($request, $menulink)
            ->withMessage(__('Item successfully updated.'));
    }
}

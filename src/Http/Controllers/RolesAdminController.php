<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use TypiCMS\Modules\Core\Http\Requests\RolesFormRequest;
use TypiCMS\Modules\Core\Models\Role;

class RolesAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('roles::admin.index');
    }

    public function create(): View
    {
        $model = new Role();
        $model->checked_permissions = [];

        return view('roles::admin.create')
            ->with(compact('model'));
    }

    public function edit(Role $role, $child = null): View
    {
        $role->checked_permissions = $role->permissions()->pluck('name')->all();

        return view('roles::admin.edit')
            ->with(['model' => $role]);
    }

    public function store(RolesFormRequest $request): RedirectResponse
    {
        $checkedPermissions = $request->input('checked_permissions', []);
        $data = $request->except(['exit', 'checked_permissions']);

        $this->storeNewPermissions($checkedPermissions);

        $role = Role::create($data);
        $role->syncPermissions($checkedPermissions);

        return $this->redirect($request, $role);
    }

    public function update(Role $role, RolesFormRequest $request): RedirectResponse
    {
        $checkedPermissions = $request->input('checked_permissions', []);
        $data = $request->except(['exit', 'checked_permissions']);
        $role->update($data);

        $this->storeNewPermissions($checkedPermissions);
        $role->syncPermissions($checkedPermissions);
        $role->forgetCachedPermissions();

        return $this->redirect($request, $role);
    }

    private function storeNewPermissions($permissions)
    {
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }
}

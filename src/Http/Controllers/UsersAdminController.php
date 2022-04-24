<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use TypiCMS\Modules\Core\Exports\UsersExport;
use TypiCMS\Modules\Core\Http\Requests\UsersFormRequest;
use TypiCMS\Modules\Core\Models\Role;
use TypiCMS\Modules\Core\Models\User;

class UsersAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('users::admin.index');
    }

    public function export(Request $request)
    {
        $filename = date('Y-m-d').' '.config('app.name').' users.xlsx';

        return Excel::download(new UsersExport(), $filename);
    }

    public function create(): View
    {
        $model = new User();
        $model->checked_roles = [];
        $roles = Role::get();

        return view('users::admin.create')
            ->with(compact('model', 'roles'));
    }

    public function edit(User $user): View
    {
        $user->checked_roles = $user->roles()->pluck('id')->all();
        $roles = Role::get();

        return view('users::admin.edit')
            ->with(['model' => $user, 'roles' => $roles]);
    }

    public function store(UsersFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->input('password'));
        $data['email_verified_at'] = Carbon::now();
        $user = User::create($data);
        $user->roles()->sync($request->input('checked_roles', []));

        return $this->redirect($request, $user);
    }

    public function update(User $user, UsersFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }
        $user->update($data);
        $user->roles()->sync($request->input('checked_roles', []));
        (new Role())->flushCache();

        return $this->redirect($request, $user);
    }
}

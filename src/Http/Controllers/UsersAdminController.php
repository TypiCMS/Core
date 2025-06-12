<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    public function export(Request $request): BinaryFileResponse
    {
        $filename = date('Y-m-d') . ' ' . config('app.name') . ' users.xlsx';

        return Excel::download(new UsersExport(), $filename);
    }

    public function create(): View
    {
        $model = new User();
        $checkedRoles = [];
        $roles = Role::query()->get();

        return view('users::admin.create')
            ->with(compact('model', 'roles', 'checkedRoles'));
    }

    public function edit(User $user): View
    {
        $checkedRoles = $user->roles()->pluck('id')->all();
        $roles = Role::query()->get();
        $passkeys = $user->passkeys()->get(['id', 'name', 'last_used_at']);

        return view('users::admin.edit')
            ->with(['model' => $user, 'roles' => $roles, 'checkedRoles' => $checkedRoles, 'passkeys' => $passkeys]);
    }

    public function store(UsersFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->string('password'));
        $data['email_verified_at'] = Carbon::now();
        $user = User::query()->create($data);
        $user->roles()->sync($request->array('checked_roles'));

        return $this->redirect($request, $user);
    }

    public function update(User $user, UsersFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->string('password'));
        } else {
            unset($data['password']);
        }
        $user->update($data);
        $user->roles()->sync($request->array('checked_roles'));

        return $this->redirect($request, $user);
    }
}

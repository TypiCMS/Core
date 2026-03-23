<?php

use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use TypiCMS\Modules\Core\Models\User;

describe('user with “edit profile” permission', function (): void {
    test('can view profile page', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('edit profile');
        $this->actingAs($user)->get(route('admin::profile'))->assertSuccessful();
    });

    test('can update profile', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('edit profile');

        $originalFirstName = $user->first_name;
        $originalLastName = $user->last_name;

        $this->actingAs($user)->put(route('admin::update-profile'), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $user->email,
        ])->assertRedirect(route('admin::profile'));

        $user->refresh();
        expect($user->first_name)->toBe('Updated');
        expect($user->last_name)->toBe('Name');
    });
});

describe('user without “edit profile” permission', function (): void {
    test('cannot view profile page', function (): void {
        $user = User::factory()->create();

        $role = Role::findByName('administrator');
        $user->assignRole($role);
        $role->revokePermissionTo('edit profile');

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user->refresh())->get(route('admin::profile'))->assertForbidden();

        $role->givePermissionTo('edit profile');
    });
});

describe('guest user', function (): void {
    test('cannot access profile page', function (): void {
        $this->get(route('admin::profile'))->assertRedirect();
    });
});

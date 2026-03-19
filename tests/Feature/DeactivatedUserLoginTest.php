<?php

use TypiCMS\Modules\Core\Models\User;

describe('deactivated user', function () {

    test('cannot request a one-time password', function () {
        $user = User::query()->firstOrFail();
        $user->update(['activated' => false]);

        $this->post('/en/otp-login', ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $user->update(['activated' => true]);
    });

    test('cannot submit a one-time password', function () {
        $user = User::query()->firstOrFail();
        $user->update(['activated' => false]);

        $this->withSession(['email' => $user->email])
            ->post('/en/otp-login-code', ['one_time_password' => '123456'])
            ->assertRedirect('/en/otp-login')
            ->assertSessionHasErrors('email');

        $user->update(['activated' => true]);
    });
});

describe('activated user', function () {

    test('can request a one-time password', function () {
        $user = User::query()->firstOrFail();
        $user->update(['activated' => true]);

        $this->post('/en/otp-login', ['email' => $user->email])
            ->assertRedirect('/en/otp-login-code')
            ->assertSessionHasNoErrors();
    });

});

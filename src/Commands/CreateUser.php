<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use TypiCMS\Modules\Core\Models\User;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    protected $name = 'typicms:user';

    protected $description = 'Creation of a superuser.';

    public function handle(): void
    {
        info('Creating a Super User…');

        $firstname = text(
            label: 'Enter your first name',
            required: 'The first name is required.',
        );
        $lastname = text(
            label: 'Enter your last name',
            required: 'The last name is required.',
        );
        $email = text(
            label: 'Enter your email address',
            required: 'The email address is required.',
            validate: fn (string $value) => match (true) {
                !filter_var($value, FILTER_VALIDATE_EMAIL) => 'The email address is not valid.',
                default => null
            }
        );
        $password = password(
            label: 'Enter a password',
            required: 'The password is required.',
            validate: fn (string $value) => match (true) {
                mb_strlen($value) < 8 => 'The password must be at least 8 characters.',
                default => null
            }
        );

        $data = [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email,
            'superuser' => 1,
            'activated' => 1,
            'password' => Hash::make($password),
            'email_verified_at' => Carbon::now(),
        ];

        try {
            User::query()->create($data);
            info('Superuser created.');
        } catch (Exception $e) {
            error('The user could not be created.');
        }
    }
}

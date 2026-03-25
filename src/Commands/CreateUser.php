<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use TypiCMS\Modules\Core\Models\User;

use function Laravel\Prompts\text;

class CreateUser extends Command
{
    protected $name = 'typicms:user';

    protected $description = 'Creation of a superuser.';

    public function handle(): void
    {
        $this->components->info('Creating a superuser.');

        $firstname = text(label: 'Enter your first name', required: 'The first name is required.');
        $lastname = text(label: 'Enter your last name', required: 'The last name is required.');
        $email = text(
            label: 'Enter your email address',
            required: 'The email address is required.',
            validate: fn (string $value): ?string => match (true) {
                !filter_var($value, FILTER_VALIDATE_EMAIL) => 'The email address is not valid.',
                default => null,
            },
        );

        $data = [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email,
            'superuser' => 1,
            'activated' => 1,
        ];

        try {
            User::query()->create($data);
            $this->components->info('Superuser created.');
        } catch (Exception) {
            $this->components->error('The user could not be created.');
        }
    }
}

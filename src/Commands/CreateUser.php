<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use TypiCMS\Modules\Core\Models\User;

class CreateUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'typicms:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creation of a superuser.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creating a Super User...');

        $firstname = $this->ask('Enter your first name');
        $lastname = $this->ask('Enter your last name');
        $email = $this->ask('Enter your email address');
        $password = $this->secret('Enter a password');

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
            User::create($data);
            $this->info('Superuser created.');
        } catch (Exception $e) {
            $this->error('User could not be created.');
        }

        $this->line('------------------');
    }
}

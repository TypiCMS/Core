<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use TypiCMS\Modules\Users\Models\User;

class Install extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'typicms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation of TypiCMS: Laravel setup, installation of composer and npm packages';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('------------------');
        $this->line('Welcome to TypiCMS');
        $this->line('------------------');

        $this->info('Publishing vendor packages...');
        $this->call('vendor:publish', ['--all' => true]);
        $this->line('------------------');

        $this->laravel['env'] = 'local';

        // Ask for database name
        $this->info('Setting up database...');
        $dbName = $this->ask('Enter a database name', $this->guessDatabaseName());

        // Set database credentials in .env and migrate
        $this->call('typicms:database', ['database' => $dbName]);
        $this->line('------------------');

        // Create a super user
        $this->createSuperUser();

        // Set cache key prefix
        $this->call('cache:prefix', ['prefix' => $dbName]);
        $this->line('------------------');

        // Composer install
        if (function_exists('system')) {
            system('find storage -type d -exec chmod 755 {} \;');
            $this->info('Directory storage is now writable (755).');
            system('find bootstrap/cache -type d -exec chmod 755 {} \;');
            $this->info('Directory bootstrap/cache is now writable (755).');
            $this->line('------------------');
            $this->info('Running yarn...');
            system('yarn');
            $this->info('npm packages installed.');
        } else {
            $this->line('You can now make /storage, /bootstrap/cache directories writable.');
            $this->line('and run composer install and npm install.');
        }

        // Done
        $this->line('------------------');
        $this->line('Done. Enjoy TypiCMS!');
    }

    /**
     * Guess database name from app folder.
     *
     * @return string
     */
    public function guessDatabaseName()
    {
        try {
            $segments = array_reverse(explode(DIRECTORY_SEPARATOR, app_path()));
            $name = explode('.', $segments[1])[0];

            return str_slug($name);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Create a superuser.
     */
    private function createSuperUser()
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
            'api_token' => Str::uuid(),
            'password' => Hash::make($password),
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

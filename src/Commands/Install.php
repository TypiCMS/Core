<?php
namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use TypiCMS\Modules\Users\Services\Registrar;

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
    protected $description = 'Installation of TypiCMS: initial Laravel setup, composer, bower, npm';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The registrar implementation.
     *
     * @var \TypiCMS\Modules\Users\Services\Registrar
     */
    protected $registrar;

    /**
     * Create a new key generator command.
     *
     * @param  \TypiCMS\Modules\Users\Services\Registrar $registrar
     * @param  Filesystem $files
     * @return void
     */
    public function __construct(Registrar $registrar, Filesystem $files)
    {
        parent::__construct();

        $this->registrar = $registrar;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        $this->line('------------------');
        $this->line('Welcome to TypiCMS');
        $this->line('------------------');

        $this->info('Publishing vendor packages...');
        $this->call('vendor:publish');
        $this->line('------------------');

        $this->laravel['env'] = 'local';

        // Ask for database name
        $this->info('Setting up database...');
        $dbName = $this->ask('Enter a database name', $this->guessDatabaseName());

        // Set database credentials in .env and migrate
        $this->call('typicms:database', ['database' => $dbName]);
        $this->line('------------------');

        // Set app key in .env
        $this->call('key:generate');
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
            system('find public/uploads -type d -exec chmod 755 {} \;');
            $this->info('Directory public/uploads is now writable (755).');
            $this->line('------------------');
            $this->info('Running npm install...');
            system('npm install');
            $this->info('npm packages installed.');
            $this->line('------------------');
            $this->info('Running bower install...');
            system('bower install');
            $this->info('Bower packages installed.');
        } else {
            $this->line('You can now make /storage and /public/uploads directories writable');
            $this->line('and run composer install, npm install and bower install.');
        }

        // Done
        $this->line('------------------');
        $this->line('Done. Enjoy TypiCMS!');

    }

    /**
     * Guess database name from app folder
     *
     * @return string
     */
    public function guessDatabaseName()
    {
        $segments = array_reverse(explode('/', app_path()));
        $name = explode('.', $segments[1])[0];
        return str_slug($name);
    }

    /**
     * Create a superuser
     */
    private function createSuperUser()
    {
        $this->info('Creating a Super User...');

        $firstname = $this->ask('Enter your first name');
        $lastname  = $this->ask('Enter your last name');
        $email     = $this->ask('Enter your email address');
        $password  = $this->secret('Enter a password');

        $data = [
            'first_name'  => $firstname,
            'last_name'   => $lastname,
            'email'       => $email,
            'superuser'   => 1,
            'activated'   => 1,
            'password'    => $password,
        ];

        try {
            $user = $this->registrar->create($data);
            $this->info('Superuser created.');
        } catch (Exception $e) {
            $this->error('User could not be created.');
        }

        $this->line('------------------');
    }
}

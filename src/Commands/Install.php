<?php

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Providers\ModuleServiceProvider;

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
        $this->call('vendor:publish', [
            '--provider' => ModuleServiceProvider::class,
            '--tag' => 'typicms-views',
            '--force' => true,
        ]);
        $this->line('------------------');

        $this->info('Publishing translations...');
        $this->call('typicms:publish-translations', ['--force' => true]);
        $this->line('------------------');

        $this->laravel['env'] = 'local';

        // Ask for database name
        $this->info('Setting up database...');
        $dbName = $this->ask('Enter a database name', $this->guessDatabaseName());

        // Set database credentials in .env and migrate
        $this->call('typicms:database', ['database' => $dbName]);
        $this->line('------------------');

        // Create a super user
        $this->call('typicms:user');

        // Composer install
        if (function_exists('system')) {
            system('chmod 755 $(find storage -type d)');
            $this->info('Directory storage is now writable (755).');
            system('chmod 755 $(find bootstrap/cache -type d)');
            $this->info('Directory bootstrap/cache is now writable (755).');
            $this->line('------------------');
            $this->info('Running yarn...');
            system('yarn');
            $this->info('npm packages installed.');
            system('npm run dev');
            system('npm run build');
            $this->info('Assets compiled.');
        } else {
            $this->line('You can now make /storage, /bootstrap/cache directories writable,');
            $this->line('run "composer install", "npm install", and finally "npm run dev".');
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

            return Str::slug($name);
        } catch (\Exception $e) {
            return '';
        }
    }
}

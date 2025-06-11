<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class Install extends Command
{
    protected $name = 'typicms:install';

    protected $description = 'Installation of TypiCMS: Laravel setup, installation of composer and npm packages';

    public function handle(): void
    {
        intro('Welcome to TypiCMS');

        $this->call('vendor:publish', ['--tag' => [
            'permission-migrations',
            'typicms-config',
            'typicms-resources',
            'typicms-public',
            'typicms-lang',
            'typicms-views',
            'typicms-migrations',
            'typicms-seeders',
        ]]);

        $this->laravel['env'] = 'local';

        // Ask for database name
        info('Setting up database…');
        $dbName = text(
            label: 'Choose a database name',
            placeholder: $this->guessDatabaseName(),
            default: $this->guessDatabaseName(),
            required: 'The database name is required.',
            hint: 'The database will be created if it doesn’t exist.',
        );

        // Set database credentials in .env and migrate
        $this->call('typicms:database', ['database' => $dbName]);

        // Create a superuser
        $this->call('typicms:user');

        // Composer install
        if (!mb_stripos(ini_get('disable_functions'), 'shell_exec')) {
            spin(
                function () {
                    shell_exec('chmod 755 $(find storage -type d) 2> /dev/null');
                    shell_exec('chmod 755 $(find bootstrap/cache -type d) 2> /dev/null');
                },
                'Set permissions on directories…'
            );

            spin(
                fn () => shell_exec('bun i 2> /dev/null'),
                'Install packages with bun…'
            );

            spin(
                fn () => shell_exec('bun run build 2> /dev/null'),
                'Compiling assets…'
            );
        } else {
            info('You can now make /storage and /bootstrap/cache directories writable,');
            info('run "composer install", "npm install", and finally "npm run dev".');
        }

        // Done
        outro('Done. Enjoy TypiCMS!');
    }

    public function guessDatabaseName(): string
    {
        try {
            $segments = array_reverse(explode(DIRECTORY_SEPARATOR, app_path()));
            $name = explode('.', $segments[1])[0];

            return Str::slug($name);
        } catch (Exception $e) {
            return '';
        }
    }
}

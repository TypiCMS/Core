<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionServiceProvider;
use TypiCMS\Modules\Core\Providers\ModuleServiceProvider;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\spin;

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
    public function handle(): void
    {
        info('Welcome to TypiCMS');

        $this->call('vendor:publish', ['--tag' => [
            'typicms-config',
            'typicms-resources',
            'typicms-public',
            'typicms-lang',
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

        // Create a super user
        $this->call('typicms:user');

        // Composer install
        if (is_callable('shell_exec') && !stripos(ini_get('disable_functions'), 'shell_exec')) {

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
        alert('Done. Enjoy TypiCMS!');
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
        } catch (Exception $e) {
            return '';
        }
    }
}

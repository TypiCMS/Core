<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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

    public function __construct(
        protected Filesystem $files,
    ) {
        parent::__construct();
    }

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
            'typicms-factories',
            'typicms-tests',
        ]]);

        app()->environment('local');

        // Ask for the database name
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

        $domain = $this->guessSiteName() . '.test';

        if ($this->canShellExec()) {
            $this->setAppUrl($domain);
            $this->secureSite($domain);
            $this->setDirectoryPermissions();
            $this->installAndBuildAssets();
        } else {
            info('Set APP_URL in your .env file and secure your site with “herd secure” or “valet secure”.');
            info(
                'Make the /storage and /bootstrap/cache directories writable, then run “npm install && npm run build”.',
            );
        }

        outro('Installation complete!');
        info("Configure Laravel’s email service, then access the admin panel at https://{$domain}/admin");
        outro('Enjoy TypiCMS!');
    }

    private function canShellExec(): bool
    {
        return (
            function_exists('shell_exec')
            && !in_array('shell_exec', explode(',', (string) ini_get('disable_functions')), true)
        );
    }

    private function setDirectoryPermissions(): void
    {
        spin(function (): void {
            shell_exec('chmod 755 $(find storage -type d) 2> /dev/null');
            shell_exec('chmod 755 $(find bootstrap/cache -type d) 2> /dev/null');
        }, 'Set permissions on directories…');
    }

    private function installAndBuildAssets(): void
    {
        $packageManager = shell_exec('which bun 2> /dev/null')
            ? 'bun'
            : (shell_exec('which npm 2> /dev/null') ? 'npm' : null);

        if (!$packageManager) {
            info('No package manager found. Please install bun or npm, run “npm install” and finally “npm run dev”.');

            return;
        }

        $installCommand = $packageManager === 'bun' ? 'bun i' : 'npm install';

        spin(
            fn (): string|false|null => shell_exec("{$installCommand} 2> /dev/null"),
            "Install packages with {$packageManager}…",
        );

        spin(fn (): string|false|null => shell_exec("{$packageManager} run build 2> /dev/null"), 'Compiling assets…');
    }

    private function setAppUrl(string $domain): void
    {
        $appUrl = "https://{$domain}";

        $contents = $this->files->get('.env');
        $contents = (string) preg_replace('/(' . preg_quote('APP_URL=', '/') . ')(.*)/', '${1}' . $appUrl, $contents);
        $this->files->put('.env', $contents);

        info("APP_URL set to {$appUrl}");
    }

    private function secureSite(string $domain): void
    {
        if (shell_exec('which herd 2> /dev/null')) {
            spin(fn (): string|false|null => shell_exec(
                "herd secure {$domain} 2> /dev/null",
            ), 'Securing site with Herd…');

            return;
        }

        if (shell_exec('which valet 2> /dev/null')) {
            spin(fn (): string|false|null => shell_exec(
                "valet secure {$domain} 2> /dev/null",
            ), 'Securing site with Valet…');
        }
    }

    private function guessSiteName(): string
    {
        return Str::slug(Str::before(basename(dirname(app_path())), '.'));
    }

    public function guessDatabaseName(): string
    {
        return $this->guessSiteName();
    }
}

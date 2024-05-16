<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputArgument;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class Database extends Command
{
    /**
     * The console command name.
     */
    protected $name = 'typicms:database';

    /**
     * The console command description.
     */
    protected $description = 'Set database credentials in .env file';

    /**
     * The filesystem instance.
     */
    protected $files;

    /**
     * Create a new key generator command.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $contents = $this->getKeyFile();

        $dbName = $this->argument('database');

        $dbAddress = text(
            label: 'What is your MySQL server address?',
            placeholder: '127.0.0.1',
            default: '127.0.0.1',
            required: 'The MySQL server address is required.',
        );
        $dbPort = text(
            label: 'What is your MySQL server port?',
            placeholder: '3306',
            default: '3306',
            required: 'The MySQL server port is required.',
        );
        $dbUserName = text(
            label: 'What is your MySQL server username?',
            default: 'root',
            required: 'The MySQL server username is required.',
        );
        $dbPassword = password(
            label: 'What is your MySQL server password?',
        );

        // Update DB credentials in .env file.
        $search = [
            '/(' . preg_quote('DB_HOST=') . ')(.*)/',
            '/(' . preg_quote('DB_PORT=') . ')(.*)/',
            '/(' . preg_quote('DB_DATABASE=') . ')(.*)/',
            '/(' . preg_quote('DB_USERNAME=') . ')(.*)/',
            '/(' . preg_quote('DB_PASSWORD=') . ')(.*)/',
        ];
        $replace = [
            '${1}' . $dbAddress,
            '${1}' . $dbPort,
            '${1}' . $dbName,
            '${1}' . $dbUserName,
            '${1}' . $dbPassword,
        ];
        $contents = preg_replace($search, $replace, $contents);

        if (!$contents) {
            throw new Exception('Error while writing credentials to .env file.');
        }

        // Set DB username and password in config
        $this->laravel['config']['database.connections.mysql.host'] = $dbAddress;
        $this->laravel['config']['database.connections.mysql.port'] = $dbPort;
        $this->laravel['config']['database.connections.mysql.username'] = $dbUserName;
        $this->laravel['config']['database.connections.mysql.password'] = $dbPassword;

        // Clear DB name in config
        unset($this->laravel['config']['database.connections.mysql.database']);

        // Force the new login to be used
        DB::purge();

        // Create database if not exists
        DB::unprepared('CREATE DATABASE IF NOT EXISTS `' . $dbName . '`');
        DB::unprepared('USE `' . $dbName . '`');
        DB::connection()->setDatabaseName($dbName);

        // Migrate DB
        if (count(Schema::getAllTables()) !== 0) {
            error('The database ' . $dbName . ' is not empty, no migration and seed were done.');
        } else {
            $this->callSilently('migrate');
            $this->callSilently('db:seed');
            info('The database ' . $dbName . ' was migrated and seeded.');
        }

        // Write to .env
        $this->files->put('.env', $contents);
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['database', InputArgument::REQUIRED, 'The database name'],
        ];
    }

    /**
     * Get the key file and return its content.
     */
    protected function getKeyFile(): string
    {
        return $this->files->exists('.env') ? $this->files->get('.env') : $this->files->get('.env.example');
    }
}

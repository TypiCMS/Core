<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

class Database extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'typicms:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set database credentials in .env file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
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
    public function handle()
    {
        $contents = $this->getKeyFile();

        $dbName = $this->argument('database');

        $dbAddress = $this->ask('What is your MySQL address?', '127.0.0.1');

        $dbPort = $this->ask('What is your MySQL port?', '3306');

        $dbUserName = $this->ask('What is your MySQL username?', 'root');

        $question = new Question('What is your MySQL password?', '<none>');
        $question->setHidden(true)->setHiddenFallback(true);
        $dbPassword = (new SymfonyQuestionHelper())->ask($this->input, $this->output, $question);
        if ($dbPassword === '<none>') {
            $dbPassword = '';
        }

        // Update DB credentials in .env file.
        $search = [
            '/('.preg_quote('DB_HOST=').')(.*)/',
            '/('.preg_quote('DB_PORT=').')(.*)/',
            '/('.preg_quote('DB_DATABASE=').')(.*)/',
            '/('.preg_quote('DB_USERNAME=').')(.*)/',
            '/('.preg_quote('DB_PASSWORD=').')(.*)/',
        ];
        $replace = [
            '${1}'.$dbAddress,
            '${1}'.$dbPort,
            '${1}'.$dbName,
            '${1}'.$dbUserName,
            '${1}'.$dbPassword,
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
        DB::unprepared('CREATE DATABASE IF NOT EXISTS `'.$dbName.'`');
        DB::unprepared('USE `'.$dbName.'`');
        DB::connection()->setDatabaseName($dbName);

        // Migrate DB
        if (Schema::hasTable('migrations')) {
            $this->error('A migrations table was found in database ['.$dbName.'], no migration and seed were done.');
        } else {
            $this->call('migrate');
            $this->call('db:seed');
        }

        // Write to .env
        $this->files->put('.env', $contents);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['database', InputArgument::REQUIRED, 'The database name'],
        ];
    }

    /**
     * Get the key file and return its content.
     *
     * @return string
     */
    protected function getKeyFile()
    {
        return $this->files->exists('.env') ? $this->files->get('.env') : $this->files->get('.env.example');
    }
}

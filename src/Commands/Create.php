<?php

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

class Create extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'typicms:create {module : The module that you want to create}
            {--force : Overwrite any existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a module in the /Modules directory.';

    /**
     * Create a new key generator command.
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
     * @return void
     */
    public function fire()
    {
        $module = str_plural(strtolower($this->argument('module')));
        if ($this->moduleExists($module)) {
            return $this->error('A module named ['.ucfirst($module).'] already exists.');
        }
        $this->generateModuleFromTemplate($module);
    }

    /**
     * Generate the module.
     *
     * @param string $module
     *
     * @return mixed
     */
    private function generateModuleFromTemplate($module)
    {
        $from = base_path('vendor/typicms/objects/src');
        $to = base_path('Modules/'.ucfirst($module));

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            $this->error("Can’t locate path: <{$from}>");
        }

        $this->info('The module ['.ucfirst($module).'] was created, you can now customize it and edit the migration file.');
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to'   => new Flysystem(new LocalAdapter($to)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            $path = $file['path'];
            if (substr($path, 0, 8) === 'database' || substr($path, 0, 15) === 'resources/views') {
                continue;
            }
            if ($file['type'] === 'file' && (!$manager->has('to://'.$file['path']) || $this->option('force'))) {
                $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
            }
        }

        $this->status($from, $to, 'Directory');
    }

    public function moduleExists($module)
    {
        $location1 = $this->files->isDirectory(base_path('Modules/'.ucfirst($module)));
        $location2 = $this->files->isDirectory(base_path('vendor/typicms/'.$module));

        return $location1 || $location2;
    }

    /**
     * Write a status message to the console.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     *
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));

        $to = str_replace(base_path(), '', realpath($to));

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }
}

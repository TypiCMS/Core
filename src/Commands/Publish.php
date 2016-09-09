<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

class Publish extends Command
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
    protected $signature = 'typicms:publish {module : The module that you want to publish}
            {--force : Overwrite any existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move a module from the vendor directory to the /Modules directory.';

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
     * @return null
     */
    public function fire()
    {
        $module = strtolower($this->argument('module'));
        if (!is_dir(base_path('vendor/typicms/'.$module))) {
            throw new Exception('Module “'.$module.'” not found in vendor directory.');
        }
        $provider = 'TypiCMS\Modules\\'.ucfirst($module).'\Providers\ModuleProvider';
        if (class_exists($provider)) {
            $this->call('vendor:publish', ['--provider' => $provider]);
            $this->publishModule($module);
            $this->uninstallFromComposer($module);
        } else {
            throw new Exception($provider.' not found, did you add it to config/app.php?');
        }
    }

    /**
     * Publishes the module.
     *
     * @param string $module
     *
     * @return mixed
     */
    private function publishModule($module)
    {
        $from = base_path('vendor/typicms/'.$module.'/src');
        $to = base_path('Modules/'.ucfirst($module));

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            $this->error("Can’t locate path: <{$from}>");
        }

        $this->info('Publishing complete for module ['.ucfirst($module).']!');
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     *
     * @return null
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

    /**
     * Write a status message to the console.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     *
     * @return null
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));

        $to = str_replace(base_path(), '', realpath($to));

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }

    /**
     * Remove a module from composer.
     *
     * @param string $module
     */
    private function uninstallFromComposer($module)
    {
        $uninstallCommand = 'composer remove typicms/'.$module;
        if (function_exists('system')) {
            system($uninstallCommand);
        } else {
            $this->line('You can now run '.$uninstallCommand.'.');
        }
    }
}

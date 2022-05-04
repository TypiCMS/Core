<?php

namespace TypiCMS\Modules\Core\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class Publish extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name of the module, pluralized and ucfirsted.
     *
     * @var string
     */
    protected $module;

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
        $this->module = mb_strtolower($this->argument('module'));
        if (!is_dir(base_path('vendor/typicms/'.$this->module))) {
            throw new Exception('Module “'.$this->module.'” not found in vendor directory.');
        }
        $provider = 'TypiCMS\Modules\\'.ucfirst($this->module).'\Providers\ModuleServiceProvider';
        if (class_exists($provider)) {
            $this->call('vendor:publish', ['--provider' => $provider]);
            $this->publishModule();
            $this->changePathForLoadViews();
            $this->uninstallFromComposer();
        } else {
            throw new Exception($provider.' not found, did you add it to config/app.php?');
        }
    }

    /**
     * Publishes the module.
     *
     * @return mixed
     */
    private function publishModule()
    {
        $from = base_path('vendor/typicms/'.$this->module.'/src');
        $to = base_path('Modules/'.ucfirst($this->module));

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            $this->error("Can’t locate path: <{$from}>");
        }

        $this->info('Publishing complete for module ['.ucfirst($this->module).']!');
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     */
    protected function publishDirectory($from, $to)
    {
        $visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

        $manager = new MountManager([
            'from' => new Flysystem(new LocalFilesystemAdapter($from)),
            'to' => new Flysystem(new LocalFilesystemAdapter($to, $visibility)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            if (mb_substr($file['path'], 0, 15) === 'resources/views' || mb_substr($file['path'], 0, 16) === 'resources/assets') {
                continue;
            }
            $path = Str::after($file['path'], 'from://');
            if ($file['type'] === 'file' && (!$manager->fileExists('to://'.$path) || $this->option('force'))) {
                $manager->write('to://'.$path, $manager->read($file['path']));
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
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));

        $to = str_replace(base_path(), '', realpath($to));

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }

    /**
     * Change the path of loadViewsFrom.
     */
    private function changePathForLoadViews()
    {
        $file = 'Modules/'.ucfirst($this->module).'/Providers/ModuleServiceProvider.php';
        $contents = $this->files->get($file);
        $contents = preg_replace('#loadViewsFrom(.*)/\', \'(.*)\'\)#', 'loadViewsFrom(resource_path(\'views\'), \'$2\')', $contents);
        $this->files->put($file, $contents);
    }

    /**
     * Remove the module from composer.
     */
    private function uninstallFromComposer()
    {
        $uninstallCommand = 'composer remove typicms/'.$this->module;
        if (function_exists('system')) {
            system($uninstallCommand);
        } else {
            $this->line('You can now run '.$uninstallCommand.'.');
        }
    }
}

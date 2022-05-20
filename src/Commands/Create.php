<?php

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class Create extends Command
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
     * The search array.
     *
     * @var array
     */
    protected $search = [
        'things',
        'thing',
        'Things',
        'Thing',
    ];

    /**
     * The replace array.
     *
     * @var array
     */
    protected $replace;

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
        if (!preg_match('/^[a-z]+$/i', $this->argument('module'))) {
            $this->error('Only alphabetic characters are allowed.');

            return;
        }

        $this->module = Str::plural(mb_ucfirst(mb_strtolower($this->argument('module'))));

        $this->replace = [
            mb_strtolower($this->module),
            mb_strtolower(Str::singular($this->module)),
            $this->module,
            Str::singular($this->module),
        ];

        if ($this->moduleExists()) {
            $this->error('A module named ['.$this->module.'] already exists.');

            return;
        }
        $this->publishModule();
        $this->renameModelsAndRepositories();
        $this->searchAndReplaceInFiles();
        $this->publishViews();
        $this->changePathForLoadViews();
        $this->publishScssFiles();
        $this->moveMigrationFile();
        $this->addTranslations();
        $this->deleteDirectories();
        $this->line('------------------');
        $this->line('<info>The module</info> <comment>'.$this->module.'</comment> <info>was created in</info> <comment>/Modules</comment><info>, customize it!</info>');
        $this->line('<info>Add</info> <comment>TypiCMS\Modules\\'.$this->module.'\Providers\ModuleServiceProvider::class,</comment>');
        $this->line('<info>to the providers array in</info> <comment>config/app.php</comment><info>.</info>');
        $this->line('<info>Run the database migration with the command</info> <comment>php artisan migrate</comment><info>.</info>');
        $this->line('<info>Run</info> <comment>npm run dev</comment> <info>to finish.</info>');
        $this->line('------------------');
    }

    /**
     * Generate the module in Modules directory.
     */
    private function publishModule()
    {
        $from = base_path('vendor/typicms/things/src');
        $to = base_path('Modules/'.$this->module);

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            $this->error("Can’t locate path: <{$from}>");
        }
    }

    /**
     * Search and remplace all occurences of ‘Things’
     * in all files by the name of the new module.
     */
    public function searchAndReplaceInFiles()
    {
        $directory = base_path('Modules/'.$this->module);

        $manager = new MountManager([
            'directory' => new Flysystem(new LocalFilesystemAdapter($directory)),
        ]);

        foreach ($manager->listContents('directory://', true) as $file) {
            if ($file['type'] === 'file') {
                $content = str_replace($this->search, $this->replace, $manager->read($file['path']));
                $manager->write($file['path'], $content);
            }
        }
    }

    /**
     * Rename files.
     */
    public function renameModelsAndRepositories()
    {
        $moduleDir = base_path('Modules/'.$this->module);
        $paths = [
            $moduleDir.'/config/things.php',
            $moduleDir.'/Models/Thing.php',
            $moduleDir.'/Facades/Things.php',
            $moduleDir.'/resources/scss/public/_thing.scss',
            $moduleDir.'/resources/scss/public/_thing-list.scss',
        ];
        foreach ($paths as $path) {
            $this->files->move($path, str_replace($this->search, $this->replace, $path));
        }
    }

    /**
     * Publish views.
     */
    public function publishViews()
    {
        $from = base_path('Modules/'.$this->module.'/resources/views');
        $to = resource_path('views/vendor/'.mb_strtolower($this->module));
        $this->publishDirectory($from, $to);
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
     * Publish scss files.
     */
    public function publishScssFiles()
    {
        $from = base_path('Modules/'.$this->module.'/resources/scss/public');
        $to = resource_path('scss/public');
        $this->publishDirectory($from, $to);
    }

    /**
     * Rename and move migration file.
     */
    public function moveMigrationFile()
    {
        $from = base_path('Modules/'.$this->module.'/database/migrations/create_things_table.php.stub');
        $to = getMigrationFileName('create_'.mb_strtolower($this->module).'_table');
        $this->files->move($from, $to);
    }

    /**
     * Add translations.
     */
    public function addTranslations()
    {
        $this->call('translations:add', ['path' => 'Modules/'.$this->module.'/lang']);
    }

    public function deleteDirectories()
    {
        $this->files->deleteDirectory(base_path('Modules/'.$this->module.'/resources'));
        $this->files->deleteDirectory(base_path('Modules/'.$this->module.'/lang'));
        $this->files->deleteDirectory(base_path('Modules/'.$this->module.'/database'));
    }

    /**
     * Publish the file to the given path.
     *
     * @param string $from
     * @param string $to
     */
    protected function publishFile($from, $to)
    {
        if ($this->files->exists($to) && !$this->option('force')) {
            return;
        }

        $this->createParentDirectory(dirname($to));

        $this->files->copy($from, $to);
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

        $this->moveManagedFiles(new MountManager([
            'from' => new Flysystem(new LocalFilesystemAdapter($from)),
            'to' => new Flysystem(new LocalFilesystemAdapter($to, $visibility)),
        ]));

        $this->status($from, $to, 'Directory');
    }

    /**
     * Move all the files in the given MountManager.
     *
     * @param \League\Flysystem\MountManager $manager
     */
    protected function moveManagedFiles($manager)
    {
        foreach ($manager->listContents('from://', true) as $file) {
            $path = Str::after($file['path'], 'from://');

            if ($file['type'] === 'file' && (!$manager->fileExists('to://'.$path) || $this->option('force'))) {
                $manager->write('to://'.$path, $manager->read($file['path']));
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param string $directory
     */
    protected function createParentDirectory($directory)
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Check if the module exists.
     *
     * @return bool
     */
    public function moduleExists()
    {
        $location1 = $this->files->isDirectory(base_path('Modules/'.$this->module));
        $location2 = $this->files->isDirectory(base_path('vendor/typicms/'.mb_strtolower($this->module)));

        return $location1 || $location2;
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
}

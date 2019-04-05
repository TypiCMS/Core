<?php

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
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
     * @return null
     */
    public function handle()
    {
        if (!preg_match('/^[a-z]+$/i', $this->argument('module'))) {
            return $this->error('Only alphabetic characters are allowed.');
        }

        $this->module = Str::plural(ucfirst(strtolower($this->argument('module'))));

        if ($this->moduleExists()) {
            return $this->error('A module named ['.$this->module.'] already exists.');
        }
        $this->publishModule();
        $this->renameModelsAndRepositories();
        $this->searchAndReplaceInFiles();
        $this->publishViews();
        $this->publishScssFiles();
        $this->moveMigrationFile();
        $this->addTranslations();
        $this->deleteResourcesDirectory();
        $this->line('------------------');
        $this->line('<info>The module</info> <comment>'.$this->module.'</comment> <info>was created in</info> <comment>/Modules</comment><info>, customize it!</info>');
        $this->line('<info>Add</info> <comment>TypiCMS\Modules\\'.$this->module.'\Providers\ModuleProvider::class,</comment>');
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
        $from = base_path('vendor/typicms/objects/src');
        $to = base_path('Modules/'.$this->module);

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            $this->error("Can’t locate path: <{$from}>");
        }
    }

    /**
     * Search and remplace all occurences of ‘Objects’
     * in all files with the name of the new module.
     */
    public function searchAndReplaceInFiles()
    {
        $directory = base_path('Modules/'.$this->module);

        $manager = new MountManager([
            'directory' => new Flysystem(new LocalAdapter($directory)),
        ]);

        $search = [
            'objects',
            'object',
            'Objects',
            'Object',
        ];
        $replace = [
            strtolower($this->module),
            strtolower(Str::singular($this->module)),
            $this->module,
            Str::singular($this->module),
        ];

        foreach ($manager->listContents('directory://', true) as $file) {
            if ($file['type'] === 'file') {
                $content = str_replace($search, $replace, $manager->read('directory://'.$file['path']));
                $manager->put('directory://'.$file['path'], $content);
            }
        }
    }

    /**
     * Rename models and repositories.
     */
    public function renameModelsAndRepositories()
    {
        $moduleDir = base_path('Modules/'.$this->module);
        $paths = [
            $moduleDir.'/Models/Object.php',
            $moduleDir.'/Facades/Objects.php',
            $moduleDir.'/Repositories/EloquentObject.php',
            $moduleDir.'/resources/scss/public/_object.scss',
            $moduleDir.'/resources/scss/public/_object-list.scss',
        ];
        foreach ($paths as $path) {
            $this->files->move($path, $this->transformFilename($path));
        }
    }

    /**
     * Publish views.
     */
    public function publishViews()
    {
        $from = base_path('Modules/'.$this->module.'/resources/views');
        $to = resource_path('views/vendor/'.strtolower($this->module));
        $this->publishDirectory($from, $to);
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
        $from = base_path('Modules/'.$this->module.'/database/migrations/2016_01_04_225000_create_objects_table.php');
        $to = base_path('database/migrations/'.date('Y_m_d_His').'_create_'.strtolower($this->module).'_table.php');
        $this->files->move($from, $to);
    }

    /**
     * Add translations.
     */
    public function addTranslations()
    {
        $this->call('translations:add', ['path' => 'Modules/'.$this->module.'/resources/lang']);
    }

    /**
     * Delete resources directory.
     *
     * @return null
     */
    public function deleteResourcesDirectory()
    {
        $this->files->deleteDirectory(base_path('Modules/'.$this->module.'/resources'));
    }

    /**
     * Rename file in path.
     *
     * @param string $path
     *
     * @return string
     */
    public function transformFilename($path)
    {
        $pathTransformed = str_replace('object', strtolower(Str::singular($this->module)), $path);
        $pathTransformed = str_replace('Object', Str::singular($this->module), $pathTransformed);

        return $pathTransformed;
    }

    /**
     * Publish the file to the given path.
     *
     * @param string $from
     * @param string $to
     *
     * @return null
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
     *
     * @return null
     */
    protected function publishDirectory($from, $to)
    {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file' && (!$manager->has('to://'.$file['path']) || $this->option('force'))) {
                $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param string $directory
     *
     * @return null
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
        $location2 = $this->files->isDirectory(base_path('vendor/typicms/'.strtolower($this->module)));

        return $location1 || $location2;
    }
}

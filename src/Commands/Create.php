<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\Console\Exception\RuntimeException;

use function Laravel\Prompts\info;

class Create extends Command
{
    protected string $module;

    /** @var array<string> */
    protected array $search = [
        'things',
        'thing',
        'Things',
        'Thing',
    ];

    /** @var array<string> */
    protected array $replace;

    protected $signature = 'typicms:create {module : The module that you want to create}
            {--force : Overwrite any existing files.}';

    protected $description = 'Create a module in the /Modules directory.';

    public function __construct(
        protected Filesystem $files,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $moduleName = $this->argument('module');
        $isAlphabetic = preg_match('/^[a-z]+$/i', $moduleName) === 1;
        if (!$isAlphabetic) {
            throw new RuntimeException('Only alphabetic characters are allowed.');
        }

        $this->module = Str::plural(mb_ucfirst(mb_strtolower($moduleName)));

        $this->replace = [
            mb_strtolower($this->module),
            mb_strtolower(Str::singular($this->module)),
            $this->module,
            Str::singular($this->module),
        ];

        if ($this->moduleExists()) {
            throw new RuntimeException('A module named ' . $this->module . ' already exists.');
        }

        $this->publishModule();
        $this->moveAndRenameFiles();
        $this->searchAndReplaceInFiles();
        $this->publishViews();
        $this->changeViewsPath();
        $this->publishScssFiles();
        $this->moveMigrationFile();
        $this->addTranslations();
        $this->deleteDirectories();
        info(
            '<info>The module</info> <comment>'
            . $this->module
            . '</comment> <info>was created in</info> <comment>/Modules</comment><info>, customize it!</info>',
        );
        info(
            '<info>Add</info> <comment>TypiCMS\Modules\\'
            . $this->module
            . '\Providers\ModuleServiceProvider::class</comment> <info>in</info> <comment>bootstrap/providers.php</comment><info>.</info>',
        );
        info(
            '<info>Run the database migration with the command</info> <comment>php artisan migrate</comment><info>.</info>',
        );
        info('<info>Run</info> <comment>npm run dev</comment> <info>to finish.</info>');
    }

    /**
     * Generate the module in Modules directory.
     */
    private function publishModule(): void
    {
        $from = base_path('vendor/typicms/things/src');
        $to = base_path('Modules/' . $this->module);

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            throw new RuntimeException(sprintf('Can’t locate path: <%s>', $from));
        }
    }

    /**
     * Search and remplace all occurences of ‘Things’
     * in all files by the name of the new module.
     */
    public function searchAndReplaceInFiles(): void
    {
        $directory = base_path('Modules/' . $this->module);

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
    public function moveAndRenameFiles(): void
    {
        $moduleDir = base_path('Modules/' . $this->module);
        $paths = [
            $moduleDir . '/config/things.php',
            $moduleDir . '/Models/Thing.php',
            $moduleDir . '/Facades/Things.php',
            $moduleDir . '/routes/things.php',
            $moduleDir . '/resources/scss/public/_thing.scss',
            $moduleDir . '/resources/scss/public/_thing-list.scss',
        ];
        foreach ($paths as $path) {
            $this->files->move($path, str_replace($this->search, $this->replace, $path));
        }
    }

    /**
     * Publish views.
     */
    public function publishViews(): void
    {
        $from = base_path('Modules/' . $this->module . '/resources/views');
        $to = resource_path('views/vendor/' . mb_strtolower($this->module));
        $this->publishDirectory($from, $to);
    }

    /**
     * Change the path of loadViewsFrom.
     */
    private function changeViewsPath(): void
    {
        $file = 'Modules/' . ucfirst($this->module) . '/Providers/ModuleServiceProvider.php';
        $contents = $this->files->get($file);
        $contents = preg_replace(
            '#loadViewsFrom(.*)/\', \'(.*)\'\)#',
            'loadViewsFrom(resource_path(\'views\'), \'$2\')',
            $contents,
        );

        $this->files->put($file, $contents);
    }

    /**
     * Publish scss files.
     */
    public function publishScssFiles(): void
    {
        $from = base_path('Modules/' . $this->module . '/resources/scss/public');
        $to = resource_path('scss/public');
        $this->publishDirectory($from, $to);
    }

    /**
     * Rename and move migration file.
     */
    public function moveMigrationFile(): void
    {
        $from = base_path('Modules/' . $this->module . '/database/migrations/create_things_table.php.stub');
        $to = getMigrationFileName('create_' . mb_strtolower($this->module) . '_table');
        $this->files->move($from, $to);
    }

    /**
     * Add translations.
     */
    public function addTranslations(): void
    {
        $this->callSilently('translations:add', ['path' => 'Modules/' . $this->module . '/lang']);
    }

    public function deleteDirectories(): void
    {
        $this->files->deleteDirectory(base_path('Modules/' . $this->module . '/resources'));
        $this->files->deleteDirectory(base_path('Modules/' . $this->module . '/lang'));
        $this->files->deleteDirectory(base_path('Modules/' . $this->module . '/database'));
    }

    /**
     * Publish the file to the given path.
     */
    protected function publishFile(string $from, string $to): void
    {
        if ($this->files->exists($to) && !$this->option('force')) {
            return;
        }

        $this->createParentDirectory(dirname($to));

        $this->files->copy($from, $to);
    }

    /**
     * Publish the directory to the given directory.
     */
    protected function publishDirectory(string $from, string $to): void
    {
        $visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

        $this->moveManagedFiles(new MountManager([
            'from' => new Flysystem(new LocalFilesystemAdapter($from)),
            'to' => new Flysystem(new LocalFilesystemAdapter($to, $visibility)),
        ]));
    }

    /**
     * Move all the files in the given MountManager.
     */
    protected function moveManagedFiles(MountManager $manager): void
    {
        foreach ($manager->listContents('from://', true) as $file) {
            $path = Str::after($file['path'], 'from://');

            if ($file['type'] === 'file' && (!$manager->fileExists('to://' . $path) || $this->option('force'))) {
                $manager->write('to://' . $path, $manager->read($file['path']));
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     */
    protected function createParentDirectory(string $directory): void
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0o755, true);
        }
    }

    /**
     * Check if the module exists.
     */
    public function moduleExists(): bool
    {
        $location1 = $this->files->isDirectory(base_path('Modules/' . $this->module));
        $location2 = $this->files->isDirectory(base_path('vendor/typicms/' . mb_strtolower($this->module)));

        return $location1 || $location2;
    }
}

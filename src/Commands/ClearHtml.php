<?php

namespace TypiCMS\Modules\Core\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClearHtml extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clear-html';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the compiled HTML files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(public_path('html'), RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->getFileName() === '.gitignore') {
                continue;
            }
            $path = $item->getRealPath();
            if ($item->isDir()) {
                rmdir($path);
            } elseif ($item->isFile()) {
                unlink($path);
            }
        }
    }
}

<?php

namespace TypiCMS\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class PagesRoutesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/pages.php');
    }
}

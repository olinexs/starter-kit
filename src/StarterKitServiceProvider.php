<?php

namespace Eoads\StarterKit;

use Illuminate\Support\ServiceProvider;
use Eoads\StarterKit\Commands\InstallCommand;

class StarterKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}

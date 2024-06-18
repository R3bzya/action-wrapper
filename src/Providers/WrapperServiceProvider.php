<?php

namespace R3bzya\ActionWrapper\Providers;

use Illuminate\Support\ServiceProvider;
use R3bzya\ActionWrapper\Console\MakeActionCommand;
use R3bzya\ActionWrapper\Console\MakeDtoCommand;

class WrapperServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootPublishes();
    }

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        $this->registerMergers();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            MakeActionCommand::class,
            MakeDtoCommand::class,
        ]);
    }

    private function bootPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/action-wrapper.php' => config_path('action-wrapper.php'),
        ], 'action-wrapper-config');

        $this->publishes([
            __DIR__.'/../Console/stubs/' => base_path('stubs'),
        ], 'action-wrapper-stubs');
    }

    private function registerMergers(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/action-wrapper.php', 'action-wrapper'
        );
    }
}
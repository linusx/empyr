<?php

namespace Linusx\Empyr\Providers;

use Illuminate\Support\ServiceProvider;

class EmpyrServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'linusx');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'linusx');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/empyr.php', 'empyr');

        // Register the service the package provides.
        $this->app->singleton('Empyr', function ($app) {
            return new Empyr;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['empyr'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../../config/empyr.php' => config_path('empyr.php'),
        ], 'empyr');
    }
}

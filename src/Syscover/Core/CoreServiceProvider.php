<?php namespace Syscover\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        // register routes
        if (!$this->app->routesAreCached())
            require __DIR__ . '/../../routes/web.php';

        // load views
        $this->loadViewsFrom(__DIR__ . '/../../views', 'core');

        // publish angular application
        $this->publishes([
            __DIR__ . '/../../../angular'	=> public_path('/packages/syscover/core')
        ]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        //
	}
}
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

        // publish angular application
        $this->publishes([
            __DIR__ . '/../../../angular'	=> public_path('/pulsar')
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
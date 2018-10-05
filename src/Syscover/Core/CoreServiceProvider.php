<?php namespace Syscover\Core;

use Illuminate\Support\ServiceProvider;
use Syscover\Core\GraphQL\CoreGraphQLServiceProvider;

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
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        // register views
        $this->loadViewsFrom(__DIR__ . '/../../views', 'core');

        // register config files
        $this->publishes([
            __DIR__ . '/../../config/pulsar-core.php'   => config_path('pulsar-core.php')
        ]);

        // register translations
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'core');
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